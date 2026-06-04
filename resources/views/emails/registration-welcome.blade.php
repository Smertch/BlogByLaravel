<x-mail::message>
# {{ __('auth.registration_welcome_heading') }}

{{ __('auth.registration_welcome_body', ['name' => $user->name, 'app' => config('app.name')]) }}

<x-mail::button :url="url('/')">
{{ __('auth.registration_welcome_button') }}
</x-mail::button>

{{ __('auth.registration_welcome_footer', ['app' => config('app.name')]) }}

</x-mail::message>
