@component('mail::message')
{{-- Greeting --}}
# @lang('Hello!')

{{-- Intro Lines --}}
@lang("Two-factor authentication has been enabled for your account.")

{{-- Outro Lines --}}
@lang("If you did not perform this action please contact your system administrator.")

{{-- Salutation --}}
@lang('Regards'),<br>{{ config('app.name') }}

@endcomponent
