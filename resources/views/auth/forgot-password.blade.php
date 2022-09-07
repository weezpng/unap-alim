<x-guest-layout>
    <x-auth-card>
        <x-slot name="logo">
            <a href="/">
                <x-application-logo class="w-20 h-20 fill-current text-gray-500" />
            </a>
        </x-slot>

        <div class="mb-4 text-sm text-gray-600">
            {{ __('Esqueceu-se da sua password?') }}
            <br />
            {{ __('Insira o seu NIM abaixo, e irá receber um email com uma password temporaria.') }}
        </div>

        <x-auth-session-status class="mb-4" :status="session('status')" />
        <x-auth-session-status class="mb-4" style="color: red;" :status="session('erro')" />
        <x-auth-validation-errors class="mb-4" :errors="$errors" />

        <form method="POST" action="{{ route('password.email') }}" style="margin-top: 1.5rem;">
            @csrf
            <div>
                <x-input id="id" style=" -webkit-appearance: none !important; margin: 0 !important;"  maxlength="8" oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);" style="text-transform:uppercase" required autofocus class="block mt-1 w-full" type="number" name="id" :value="old('NIM')" required autofocus />
            </div>
            <div class="flex items-center justify-end mt-4" style="margin-top: 1.5rem;">
                <a class="underline text-sm text-gray-600 hover:text-gray-900" href="{{ route('login') }}" style="margin-right: 1.5rem;">
                    {{ __('Iniciar sessão') }}
                </a>
                <x-button>
                    {{ __('Enviar email') }}
                </x-button>
            </div>
        </form>
    </x-auth-card>
</x-guest-layout>
