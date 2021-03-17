<x-guest-layout>
    <x-auth-card>
        <x-slot name="logo">
            <a href="/">
                <x-application-logo
                    class="w-20 h-20 fill-current text-gray-500"
                />
            </a>
        </x-slot>

        <!-- Session Status -->
        <x-auth-session-status class="mb-4" :status="session('status')" />

        <!-- Validation Errors -->
        <x-auth-validation-errors class="mb-4" :errors="$errors" />

        <form name="two_factor_auth_form" method="POST" action="{{ route('login-2fa') }}">
            @csrf

            <!-- OTP -->
            <div>
                <x-label for="otp" :value="__('Enter security code.')" />

                <x-input
                    id="otp"
                    class="block mt-1 w-full"
                    type="text"
                    name="{{ config('google2fa.otp_input') }}"
                    required
                    autofocus
                />
            </div>

            <!-- Select option -->
            <div class="flex flex-col items-stretch	justify-center">
                <div class="flex flex-row flex-grow items-center justify-around">
                    <label class="inline-flex items-center mt-3">
                        <input type="radio" name="auth_method" value="otp" class="form-radio h-5 w-5 text-gray-600" checked>
                        <span class="ml-2 text-gray-700">{{ __('OTP') }}</span>
                    </label>
                    <label class="inline-flex items-center mt-3">
                        <input type="radio" name="auth_method" value="recovery_code" class="form-radio h-5 w-5 text-gray-600">
                        <span class="ml-2 text-gray-700">{{ __('Recovery code') }}</span>
                    </label>
                </div>
            </div>

            <div class="flex items-center justify-end mt-4">
                <x-button class="ml-3">
                    {{ __('Authenticate') }}
                </x-button>
            </div>
        </form>
        <script>
            let otp_field_name = '{{ config('google2fa.otp_input') }}';
            let recovery_field_name = '{{ config('google2fa.recovery_code_input') }}';

            document.forms.two_factor_auth_form.auth_method.forEach(radio => {
                radio.addEventListener('change', () => {

                    let method_val = document.forms.two_factor_auth_form.auth_method.value;
                    let input = document.querySelector("#otp");

                    switch (method_val) {
                        case "otp":
                            input.setAttribute("name", otp_field_name);
                            break;
                        case "recovery_code":
                            input.setAttribute("name", recovery_field_name);
                            break;
                        default:
                            break;
                    }
                });
            });
        </script>
    </x-auth-card>
</x-guest-layout>
