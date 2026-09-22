<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Invoice {{ $invoice->invoice_number }}</title>
    <style>
        @page { margin: 28px 32px; }
        * { box-sizing: border-box; }
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 11px;
            color: #16120f;
            line-height: 1.45;
            margin: 0;
        }
        .muted { color: #6b635c; }
        .gold { color: #9b7b4b; }
        .dark { color: #16120f; }
        .right { text-align: right; }
        .center { text-align: center; }
        .bold { font-weight: bold; }
        table { border-collapse: collapse; width: 100%; }
        .w-full { width: 100%; }
        .spacer { height: 14px; }
        .header-bar {
            background: #16120f;
            color: #f6f1ea;
            padding: 18px 20px;
        }
        .header-bar td { vertical-align: middle; color: #f6f1ea; }
        .accent {
            height: 4px;
            background: #c4a574;
        }
        .badge {
            display: inline-block;
            background: #c4a574;
            color: #16120f;
            padding: 4px 10px;
            font-size: 10px;
            font-weight: bold;
            letter-spacing: 0.08em;
            text-transform: uppercase;
        }
        .section-title {
            font-size: 10px;
            letter-spacing: 0.16em;
            text-transform: uppercase;
            color: #9b7b4b;
            margin: 0 0 6px 0;
            font-weight: bold;
        }
        .card {
            border: 1px solid #e8e0d6;
            padding: 12px 14px;
            background: #fffcf8;
        }
        .items th {
            background: #16120f;
            color: #f6f1ea;
            padding: 8px 8px;
            font-size: 9px;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            text-align: left;
        }
        .items td {
            padding: 9px 8px;
            border-bottom: 1px solid #ebe4da;
            vertical-align: top;
        }
        .items tr:nth-child(even) td { background: #faf6f0; }
        .totals td {
            padding: 5px 8px;
        }
        .totals .grand td {
            background: #16120f;
            color: #f6f1ea;
            font-weight: bold;
            padding: 10px 8px;
        }
        .totals .grand .gold-cell { color: #c4a574; }
        .footer {
            margin-top: 22px;
            border-top: 1px solid #e8e0d6;
            padding-top: 12px;
            font-size: 9px;
            color: #6b635c;
        }
        .logo { max-height: 46px; }
        h1 {
            margin: 0;
            font-size: 22px;
            letter-spacing: 0.04em;
            color: #f6f1ea;
        }
        .meta-label { color: #c4a574; font-size: 9px; text-transform: uppercase; letter-spacing: 0.1em; }
    </style>
</head>
<body>
@php
    $org = $invoice->organization;
    $customer = $invoice->customer;
    $project = $invoice->project;
    $logo = public_path('Logo/logo-web.png');
    $currency = $org?->currency ?: 'INR';
    $fmt = fn (?int $minor) => number_format(((int) $minor) / 100, 2);
    $primary = $org?->brand_primary ?: '#C4A574';
    $dark = $org?->brand_dark ?: '#16120f';
    $cgst = (int) $invoice->items->sum('cgst_amount');
    $sgst = (int) $invoice->items->sum('sgst_amount');
    $igst = (int) $invoice->items->sum('igst_amount');
    $taxable = (int) ($invoice->subtotal - $invoice->discount);
    if ($taxable < 0) { $taxable = 0; }
    // Fallback if GST fields empty on older invoices
    if ($cgst + $sgst + $igst === 0 && (int) $invoice->tax > 0) {
        if ($invoice->is_interstate) {
            $igst = (int) $invoice->tax;
        } else {
            $cgst = (int) round($invoice->tax / 2);
            $sgst = (int) $invoice->tax - $cgst;
        }
    }
    $status = strtoupper(str_replace('_', ' ', $invoice->status?->value ?? 'draft'));
@endphp

<table class="w-full header-bar" style="background: {{ $dark }};">
    <tr>
        <td width="55%">
            @if (is_file($logo))
                <img class="logo" src="{{ $logo }}" alt="{{ $org?->name }}">
            @else
                <div style="font-size:18px;font-weight:bold;color:#f6f1ea;">{{ $org?->name }}</div>
            @endif
            <div style="margin-top:8px;font-size:10px;color:#d8cbb8;">
                {{ $org?->legal_name ?: $org?->name }}
            </div>
        </td>
        <td width="45%" class="right">
            <div class="badge" style="background: {{ $primary }};">Tax Invoice</div>
            <h1 style="margin-top:8px;">{{ $invoice->invoice_number }}</h1>
            <div style="margin-top:8px;font-size:10px;color:#d8cbb8;">
                <span class="meta-label">Status</span> {{ $status }}
            </div>
        </td>
    </tr>
</table>
<div class="accent" style="background: {{ $primary }};"></div>

<div class="spacer"></div>

<table class="w-full">
    <tr>
        <td width="50%" style="padding-right:8px;vertical-align:top;">
            <div class="section-title">From / Seller</div>
            <div class="card">
                <div class="bold" style="font-size:13px;">{{ $org?->legal_name ?: $org?->name }}</div>
                <div class="muted" style="margin-top:4px;">
                    {{ $org?->address }}<br>
                    {{ collect([$org?->city, $org?->state, $org?->country])->filter()->implode(', ') }}
                </div>
                <div style="margin-top:8px;">
                    @if ($org?->tax_id)
                        <div><span class="gold bold">GSTIN:</span> {{ $org->tax_id }}</div>
                    @endif
                    @if ($org?->email)
                        <div><span class="gold bold">Email:</span> {{ $org->email }}</div>
                    @endif
                    @if ($org?->phone)
                        <div><span class="gold bold">Phone:</span> {{ $org->phone }}</div>
                    @endif
                    @if ($org?->website)
                        <div><span class="gold bold">Web:</span> {{ $org->website }}</div>
                    @endif
                </div>
            </div>
        </td>
        <td width="50%" style="padding-left:8px;vertical-align:top;">
            <div class="section-title">Bill To / Buyer</div>
            <div class="card">
                <div class="bold" style="font-size:13px;">{{ $invoice->billing_name ?: $customer?->name }}</div>
                <div class="muted" style="margin-top:4px;">
                    @if ($invoice->billing_address)
                        {{ $invoice->billing_address }}<br>
                    @elseif ($customer)
                        {{ collect([$customer->city])->filter()->implode(', ') }}
                    @endif
                </div>
                <div style="margin-top:8px;">
                    @if ($invoice->billing_gstin)
                        <div><span class="gold bold">GSTIN:</span> {{ $invoice->billing_gstin }}</div>
                    @endif
                    @if ($customer?->email)
                        <div><span class="gold bold">Email:</span> {{ $customer->email }}</div>
                    @endif
                    @if ($customer?->phone)
                        <div><span class="gold bold">Phone:</span> {{ $customer->phone }}</div>
                    @endif
                    @if ($invoice->place_of_supply)
                        <div><span class="gold bold">Place of supply:</span> {{ $invoice->place_of_supply }}</div>
                    @endif
                </div>
            </div>
        </td>
    </tr>
</table>

<div class="spacer"></div>

<table class="w-full">
    <tr>
        <td width="33%" style="padding-right:6px;vertical-align:top;">
            <div class="card">
                <div class="section-title">Invoice date</div>
                <div class="bold">{{ optional($invoice->issue_date)?->format('d M Y') ?: '—' }}</div>
            </div>
        </td>
        <td width="33%" style="padding:0 3px;vertical-align:top;">
            <div class="card">
                <div class="section-title">Due date</div>
                <div class="bold">{{ optional($invoice->due_date)?->format('d M Y') ?: '—' }}</div>
            </div>
        </td>
        <td width="34%" style="padding-left:6px;vertical-align:top;">
            <div class="card">
                <div class="section-title">Project</div>
                <div class="bold">{{ $project?->title ?: 'General services' }}</div>
                @if ($project?->project_number)
                    <div class="muted">{{ $project->project_number }}
                        @if ($project->venue || $project->city)
                            · {{ collect([$project->venue, $project->city])->filter()->implode(', ') }}
                        @endif
                    </div>
                @endif
            </div>
        </td>
    </tr>
</table>

<div class="spacer"></div>

<table class="items w-full">
    <thead>
        <tr>
            <th width="5%">#</th>
            <th width="38%">Description</th>
            <th width="10%">HSN/SAC</th>
            <th width="7%" class="right">Qty</th>
            <th width="12%" class="right">Rate</th>
            <th width="8%" class="right">GST%</th>
            <th width="20%" class="right">Amount</th>
        </tr>
    </thead>
    <tbody>
        @forelse ($invoice->items as $index => $item)
        <tr>
            <td>{{ $index + 1 }}</td>
            <td>
                <div class="bold">{{ $item->description }}</div>
                @if ($item->cgst_amount || $item->sgst_amount || $item->igst_amount)
                    <div class="muted" style="font-size:9px;margin-top:2px;">
                        Taxable {{ $fmt($item->taxable_amount ?: $item->amount) }}
                        @if ($item->igst_amount)
                            · IGST {{ $fmt($item->igst_amount) }}
                        @else
                            · CGST {{ $fmt($item->cgst_amount) }} · SGST {{ $fmt($item->sgst_amount) }}
                        @endif
                    </div>
                @endif
            </td>
            <td>{{ $item->hsn_sac ?: '998386' }}</td>
            <td class="right">{{ $item->quantity }}</td>
            <td class="right">{{ $fmt($item->unit_amount) }}</td>
            <td class="right">{{ (int) ($item->gst_rate ?? 18) }}%</td>
            <td class="right bold">{{ $fmt($item->amount) }}</td>
        </tr>
        @empty
        <tr>
            <td colspan="7" class="center muted">No line items</td>
        </tr>
        @endforelse
    </tbody>
</table>

<div class="spacer"></div>

<table class="w-full">
    <tr>
        <td width="52%" style="vertical-align:top;padding-right:12px;">
            <div class="section-title">Notes / Terms</div>
            <div class="card muted">
                {{ $invoice->notes ?: 'Payment is due as per the schedule above. Please quote the invoice number in all bank transfers. This is a computer-generated tax invoice.' }}
            </div>
            <div style="margin-top:12px;" class="card">
                <div class="section-title">Bank / payment</div>
                <div class="muted">
                    Please pay to <span class="dark bold">{{ $org?->legal_name ?: $org?->name }}</span>.
                    Balance due: <span class="dark bold">{{ $currency }} {{ $fmt($invoice->balance()) }}</span>
                </div>
            </div>
        </td>
        <td width="48%" style="vertical-align:top;">
            <table class="totals w-full">
                <tr>
                    <td>Subtotal</td>
                    <td class="right">{{ $currency }} {{ $fmt($invoice->subtotal) }}</td>
                </tr>
                @if ($invoice->discount)
                <tr>
                    <td>Discount</td>
                    <td class="right">− {{ $currency }} {{ $fmt($invoice->discount) }}</td>
                </tr>
                @endif
                <tr>
                    <td>Taxable value</td>
                    <td class="right">{{ $currency }} {{ $fmt($taxable) }}</td>
                </tr>
                @if ($igst > 0)
                <tr>
                    <td>IGST</td>
                    <td class="right">{{ $currency }} {{ $fmt($igst) }}</td>
                </tr>
                @else
                <tr>
                    <td>CGST</td>
                    <td class="right">{{ $currency }} {{ $fmt($cgst) }}</td>
                </tr>
                <tr>
                    <td>SGST</td>
                    <td class="right">{{ $currency }} {{ $fmt($sgst) }}</td>
                </tr>
                @endif
                <tr>
                    <td>Total tax</td>
                    <td class="right">{{ $currency }} {{ $fmt($invoice->tax) }}</td>
                </tr>
                <tr class="grand">
                    <td>Grand total</td>
                    <td class="right gold-cell">{{ $currency }} {{ $fmt($invoice->total) }}</td>
                </tr>
                <tr>
                    <td>Amount paid</td>
                    <td class="right">{{ $currency }} {{ $fmt($invoice->paid) }}</td>
                </tr>
                <tr>
                    <td class="bold">Balance due</td>
                    <td class="right bold">{{ $currency }} {{ $fmt($invoice->balance()) }}</td>
                </tr>
            </table>
        </td>
    </tr>
</table>

<div class="footer">
    <table class="w-full">
        <tr>
            <td width="60%">
                HSN/SAC {{ $invoice->items->first()?->hsn_sac ?: '998386' }} — Photography / videography services.<br>
                Generated for {{ $org?->name }} · {{ now()->format('d M Y H:i') }}
            </td>
            <td width="40%" class="right">
                <div class="bold dark">For {{ $org?->legal_name ?: $org?->name }}</div>
                <div style="margin-top:28px;" class="muted">Authorized signatory</div>
            </td>
        </tr>
    </table>
</div>
</body>
</html>
