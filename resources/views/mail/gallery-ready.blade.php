@component('mail::message')
<img src="{{ url('/Logo/logo-web.png') }}" alt="Unik Studio" width="180">

# Your gallery is ready

This is a transactional note from Unik Studio. Open your client space for details.

@component('mail::button', ['url' => config('app.url')])
Open Unik Studio
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent
