<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class ConfirmablePasswordController extends Controller
{
    /**
     * Show the confirm password view.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function show(Request $request)
    {
        $id = Auth::user()->id;
        while ((strlen((string)$id)) < 8) { $id = 0 . (string)$id; }
        return view('auth.confirm-password', ['id' => $id]);
    }

    /**
     * Confirm the user's password.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return mixed
     */
    public function store(Request $request)
    {        
        $id = Auth::user()->id;
        while ((strlen((string)$id)) < 8) { $id = 0 . (string)$id; }
        $ad_url = "exercito.local";
        $user_id = "exercito\\" . $id;
        $user_pw = $request->password;
        $ldap = ldap_connect($ad_url);
        if ($bind = ldap_bind($ldap, $user_id , $user_pw)) {
            $request->session()->put('auth.password_confirmed_at', time());            
            return redirect()->intended(RouteServiceProvider::HOME);
        } else {
            throw ValidationException::withMessages([
                'password' => __('auth.password'),
            ]);
        }
    }
}
