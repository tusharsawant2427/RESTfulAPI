@component('mail::message')
# Hello {{ $user->name }}

Thank you for creating an account with us. Please verify your email using this link:

@component('mail::button', ['url' => route('verify', $user->verification_token)])
    Verify Email
@endcomponent
Thanks, <br>
Restful API
@endcomponent
