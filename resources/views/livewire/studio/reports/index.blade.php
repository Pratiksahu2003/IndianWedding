<div>
<h1 class="font-[Cormorant_Garamond] text-4xl">Reports</h1>
<div class="mt-8 grid gap-4 md:grid-cols-4">
<div class="rounded-3xl bg-white p-5">Leads<br><strong>{{ $leadTotal }}</strong></div>
<div class="rounded-3xl bg-white p-5">Converted<br><strong>{{ $converted }}</strong></div>
<div class="rounded-3xl bg-white p-5">Projects<br><strong>{{ $projects }}</strong></div>
<div class="rounded-3xl bg-white p-5">Revenue<br><strong>{{ \App\Support\Money::format($revenue) }}</strong></div>
</div>
<button wire:click="exportLeads" class="mt-6 rounded-full bg-[#16120f] px-4 py-2 text-white">Export leads CSV</button>
</div>
