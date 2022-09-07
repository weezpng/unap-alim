<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Config;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        $msgs_index =  \App\Models\PlatformWarnings::where('to_show', 'LOGIN')->get()->all();
        if (empty($msgs_index)) $msgs_index = null;

        return view('auth.login', [
            'msgs' => $msgs_index
        ]);
    }
    /**
     * Handle an incoming authentication request.
     *
     * @param  \App\Http\Requests\Auth\LoginRequest  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(LoginRequest $request)
    {

        $user = \App\Models\User::where('id', $request->id)->first();
        if ($user) {
          if ($user->account_verified=='N') return redirect()->back()->withErrors(['A sua conta ainda não se encontra activada.']);
        }

        $request->authenticate();

        $request->session()->regenerate();

        $user['last_login'] = now();
        $user->save();
        
        session(['lock-expires-at' => now()->addMinutes($request->user()->getLockoutTime())]);

        return redirect()->intended(RouteServiceProvider::HOME);
    }

    /**
     * Destroy an authenticated session.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Request $request)
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
