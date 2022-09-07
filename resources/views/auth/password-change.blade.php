<x-guest-layout>
    <x-auth-card>
        <x-slot name="logo">
            <a href="/">
                <x-application-logo class="w-20 h-20 fill-current text-gray-500" />
            </a>
        </x-slot>

        <!-- Validation Errors -->
        <x-auth-validation-errors class="mb-4" :errors="$errors" />
        <label class="block font-medium text-sm text-gray-700" style="margin-top: 10px !important;">
            É necessário redefinir a sua password.
        </label>

        <form method="POST" action="{{ route('reset.pw') }}">
            @csrf
            <div class="mt-4" style="display: none !important;">
                <x-input id="id" class="block mt-1 w-full" name="id" value="{{ Auth::user()->id }}"/>
            </div>
            <!-- Password -->
            <div class="mt-4">
                <x-label for="password" :value="__('Nova password')" />
                <x-input id="password" class="block mt-1 w-full" type="password" name="password" required />
            </div>

            <!-- Confirm Password -->
            <div class="mt-4">
                <x-label for="password_confirmation" :value="__('Confirmar password')" />

                <x-input id="password_confirmation" class="block mt-1 w-full"
                                    type="password"
                                    name="password_confirmation" required />
            </div>

            <div class="flex items-center justify-end mt-4">
                <x-button>
                    {{ __('Continuar') }}
                </x-button>
            </div>
        </form>

    </x-auth-card>
</x-guest-layout>
