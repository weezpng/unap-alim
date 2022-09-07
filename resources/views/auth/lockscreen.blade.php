<x-guest-layout>
    <x-auth-card>
        <x-slot name="logo">
            <a href="/">
                <x-application-logo class="w-20 h-20 fill-current text-gray-500" />
            </a>
        </x-slot>
          <a href="{{ route('login') }}">
            <x-button style="width: 100%; text-align: center !important; margin-top: 10px; font-size: .9rem; display: block !important; font-weight: 100 !important; ">
                Entrar
            </x-button>
          </a>

          <a href="{{ route('register') }}">
            <x-button style="width: 100%; text-align: center !important; margin-top: 10px; font-size: .9rem; display: block !important; font-weight: 100 !important; ">
                Registar
            </x-button>
          </a>
    </x-auth-card>
</x-guest-layout>
