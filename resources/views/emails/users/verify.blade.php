@component('mail::message')
{{-- Greeting --}}
# @lang('Hello!')

{{-- Intro Lines --}}
@lang("Please click the button below to verify your email address.")

{{-- Action Button --}}
@component('mail::button', ['url' => $pwaURL, 'color' => 'primary'])
@lang("Verify Email Address")
@endcomponent

{{-- Outro Lines --}}
@lang("This verification link will expire in :count minutes.", ['count' => config('auth.verification.expire', 60)])
<br>
@lang("If you did not create an account, no further action is required.")

{{-- Salutation --}}
@lang('Regards'),<br>{{ config('app.name') }}

{{-- Subcopy --}}
@slot('subcopy')
@lang(
"If you’re having trouble clicking the \":actionText\" button, copy and paste the URL below\n".
'into your web browser: [:actionURL](:actionURL)',
    [
        'actionText' => __("Verify Email Address"),
        'actionURL' => $verificationURL
    ]
)
@endslot
@endcomponent
