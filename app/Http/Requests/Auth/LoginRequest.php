<?php

namespace App\Http\Requests\Auth;

use Illuminate\Auth\Events\Lockout;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class LoginRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'id' => 'required|string|min:8|max:8',
            'password' => 'required|string',
        ];
    }

    /**
     * Autentica o utilizador utilizando o protocolo LDAP.
     *
     * @return void
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function authenticate()
    {

        $login_internal = array("id" => $this->id, "password" => $this->id);

        $this->ensureIsNotRateLimited();

            $ad_url = "exercito.local";
            $user_id = "exercito\\" . $this->id;
            $user_pw = $this->password;
            $ldap = ldap_connect($ad_url) or die("Impossivel conectar serviço ActiveDirectory");

            try {
                $bind = ldap_bind($ldap, $user_id , $user_pw);
            } catch (\Exception $e) {
                $message = $e->getMessage();
                if(str_contains($message, "Unable to bind to server: Invalid credentials")){
                    throw ValidationException::withMessages([
                        'id' => __('auth.failed'),
                    ]);
                } else {
                    $to_email = \env('MAIL_FROM_ADDRESS');
                    $data['title'] = "Erro de login";
                    $data['message'] = $message;
                    Mail::to($to_email)->send(new DebugInfo($data));
                     throw ValidationException::withMessages([
                        'id' => $message,
                    ]);
                }
            }
            
            if ($bind) {
                if (! Auth::attempt($login_internal, $this->filled('remember'))) {
                    RateLimiter::hit($this->throttleKey());
                    throw ValidationException::withMessages([
                        'id' => __('auth.failed'),
                    ]);
                }
                Auth::logoutOtherDevices($this->id);
            } else {
                throw ValidationException::withMessages([
                    'id' => __('auth.failed'),
                ]);
            }

        RateLimiter::clear($this->throttleKey());
    }

    /**
     * Ensure the login request is not rate limited.
     *
     * @return void
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function ensureIsNotRateLimited()
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout($this));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'id' => trans('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    /**
     * Get the rate limiting throttle key for the request.
     *
     * @return string
     */
    public function throttleKey()
    {
        return Str::lower($this->input('id')).'|'.$this->ip();
    }
}
