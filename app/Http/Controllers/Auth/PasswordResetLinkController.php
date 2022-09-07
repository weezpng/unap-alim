<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use App\Mail\passwordResetNotification;
use Illuminate\Support\Facades\Mail;

class PasswordResetLinkController extends Controller
{
    public function create()
    {
        return view('auth.forgot-password');
    }

    public function store(Request $request)
    {

        $request->validate([
            'id' => 'required',
        ]);

        $user = \App\Models\User::where('id', $request->id)->first();
        $newPassword = \Str::random(8);
        $user->password = \Hash::make($newPassword);
        $user->mustResetPassword = 'Y';
        $token = \Str::random(20);
        $to_email = $user->email;

        $data = array();
        $data['posto']=strtoupper($user['posto']);
        $data['nome']=strtoupper($user['name']);
        $data['pw']=$newPassword;

        try {

          if (!filter_var($to_email, FILTER_VALIDATE_EMAIL)) {
            throw new \Exception('Email inválido.');
          }
          
          foreach ([$to_email] as $recipient) {
            Mail::to($recipient)->send(new passwordResetNotification($data));
          }

          $user->save();
          return back()->with('status', __('Email enviado. Verifique a sua caixa de correio.'))->withInput($request->only('id'));

        } catch (\Exception $e) {
          return back()->with('erro', __('Impossivel enviar email. Peça a admistração para fazer o reset da password.'))->withInput($request->only('id'));
        }
    }
}
