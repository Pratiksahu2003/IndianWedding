<html><body style="font-family: DejaVu Sans, sans-serif; color:#16120f;">
@php
    $logo = public_path('Logo/logo-web.png');
@endphp
@if (is_file($logo))
<p><img src="{{ $logo }}" alt="Unik Studio" style="height:42px;"></p>
@endif
<h1>Invoice {{ $invoice->invoice_number }}</h1>
<p>{{ $invoice->organization?->name }}<br>{{ $invoice->customer?->name }}</p>
<p>Issue: {{ optional($invoice->issue_date)?->toFormattedDateString() }}
@if ($invoice->due_date)
 · Due: {{ $invoice->due_date->toFormattedDateString() }}
@endif
</p>
<table width="100%" cellpadding="6">
@forelse ($invoice->items as $item)
<tr><td>{{ $item->description }}</td><td align="right">{{ number_format($item->amount/100, 2) }}</td></tr>
@empty
<tr><td colspan="2">No line items</td></tr>
@endforelse
<tr><td><strong>Total</strong></td><td align="right"><strong>{{ number_format($invoice->total/100, 2) }}</strong></td></tr>
<tr><td>Paid</td><td align="right">{{ number_format(($invoice->paid ?? 0)/100, 2) }}</td></tr>
</table>
</body></html>
