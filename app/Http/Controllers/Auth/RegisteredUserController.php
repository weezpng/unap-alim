<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        $unidades = \App\Models\unap_unidades::get()->all();

        $msgs_index =  \App\Models\PlatformWarnings::where('to_show', 'REGISTER')->get()->all();
        if (empty($msgs_index)) $msgs_index = null;

        return view('auth.register', [
          'unidades' => $unidades,
          'msgs' => $msgs_index,
        ]);
    }

    public function login_or_register()
    {
        return view('auth.lockscreen');
    }


    /**
     * Handle an incoming registration request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request)
    {

        $request->validate([
            'nim'     => 'required|string|min:8|max:8',
            'name'    => 'required|string|max:20',
            'email'   => 'required|string|email|max:255|unique:users',
            'unidade' => 'required|string',
        ]);

        $isThereUserWithNIM = User::where('id', $request->nim)->first();
        if ($isThereUserWithNIM!=null) {
          return redirect()->back()->withErrors(['Este NIM já se encontra registado.']);
        }

        $isThereChildrenWithNIM = \App\Models\users_children::where('childID', $request->nim)->first();
        if ($isThereChildrenWithNIM!=null) {
          return redirect()->back()->withErrors(['Existe um utilizador associado com esse NIM. Para iniciar sessão nessa conta, é necessário converte-la para utilizador regular.']);
        }

        $isThereToken = \App\Models\express_account_verification_tokens::where('NIM', $request->nim)->first();
        $verify = ($isThereToken!=NULL ? 'Y' : 'N');

        $pedido = new \App\Models\QRsGerados();
        $pedido->NIM = $request->nim;
        $pedido->save();

        Auth::login($user = User::create([
            'id' => $request->nim,
            'name' => $request->name,
            'email' => $request->email,
            'account_verified' => $verify,
            'unidade' => $request->unidade,
            'telf' => (isset($request->telf)) ? $request->telf : '',
            'seccao' => $request->section,
            'password' => Hash::make($request->nim),
        ]));

        event(new Registered($user));

        return redirect(RouteServiceProvider::LOGIN);
    }
}
