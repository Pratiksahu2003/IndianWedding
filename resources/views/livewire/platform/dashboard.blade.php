<div>
<div class="grid gap-4 md:grid-cols-3">
<div class="rounded-3xl bg-white p-6">Studios<br><strong>{{ $organizations->count() }}</strong></div>
<div class="rounded-3xl bg-white p-6">Users<br><strong>{{ $users }}</strong></div>
<div class="rounded-3xl bg-white p-6">Processed volume<br><strong>{{ \App\Support\Money::format($revenue) }}</strong></div>
</div>
<table class="mt-8 w-full text-sm bg-white rounded-3xl overflow-hidden">
<thead class="bg-slate-50 text-left"><tr><th class="p-4">Studio</th><th>Plan</th><th>Status</th></tr></thead>
<tbody>
@foreach ($organizations as $org)
<tr class="border-t"><td class="p-4">{{ $org->name }}</td><td>{{ $org->plan?->name }}</td><td>{{ $org->subscription_status }}</td></tr>
@endforeach
</tbody>
</table>
</div>
