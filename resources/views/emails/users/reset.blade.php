@component('mail::message')
{{-- Greeting --}}
# @lang('Hello!')

{{-- Intro Lines --}}
@lang("You are receiving this email because we received a password reset request for your account.")

{{-- Action Button --}}
@component('mail::button', ['url' => $actionUrl, 'color' => 'primary'])
@lang("Reset Password")
@endcomponent

{{-- Outro Lines --}}
@lang("This password reset link will expire in :count minutes.", ['count' => config('auth.passwords.'.config('auth.defaults.passwords').'.expire')])
@lang("If you did not request a password reset, no further action is required.")

{{-- Salutation --}}
@lang('Regards'),<br>{{ config('app.name') }}

{{-- Subcopy --}}
@slot('subcopy')
@lang(
    "If you’re having trouble clicking the \":actionText\" button, copy and paste the URL below\n".
    'into your web browser: [:actionURL](:actionURL)',
    [
        'actionText' => __("Reset Password"),
        'actionURL' => $actionUrl,
    ]
)
@endslot

token: {{ $token }}

@endcomponent
