<html><body style="font-family: DejaVu Sans, sans-serif; color:#16120f;">
<p><img src="{{ public_path('Logo/logo-web.png') }}" alt="Unik Studio" style="height:42px;"></p>
<h1>Invoice {{ $invoice->invoice_number }}</h1>
<p>{{ $invoice->organization?->name }}<br>{{ $invoice->customer?->name }}</p>
<table width="100%" cellpadding="6">
@foreach ($invoice->items as $item)
<tr><td>{{ $item->description }}</td><td align="right">{{ number_format($item->amount/100, 2) }}</td></tr>
@endforeach
<tr><td><strong>Total</strong></td><td align="right"><strong>{{ number_format($invoice->total/100, 2) }}</strong></td></tr>
</table>
</body></html>
