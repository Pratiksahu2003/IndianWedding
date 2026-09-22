@component('mail::message')
# A payment is due

This is a transactional note from Lumina Atelier. Open your client space for details.

@component('mail::button', ['url' => config('app.url')])
Open Lumina
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent
