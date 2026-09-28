<div class="space-y-8">
    <div class="rounded-3xl bg-white p-8">
        <p class="text-xs uppercase tracking-[0.2em] opacity-50">Service</p>
        <h1 class="mt-2 font-[Cormorant_Garamond] text-4xl">{{ $package->name }}</h1>
        <p class="mt-3 max-w-3xl text-[#16120f]/70">{{ $package->description }}</p>
        <p class="mt-4 text-lg">{{ \App\Support\Money::format($package->price) }}</p>
        <div class="mt-6 flex flex-wrap gap-3 text-sm">
            @if ($package->is_public)
                <a href="{{ $package->publicUrl() }}" target="_blank" class="rounded-full bg-[#16120f] px-4 py-2 text-white">View public page</a>
            @endif
            <a href="{{ route('app.packages.index') }}" class="rounded-full bg-[#f6f1ea] px-4 py-2">All services</a>
            @can('update', $package)
            <a href="{{ route('app.packages.edit', $package) }}" class="rounded-full bg-[#16120f] px-4 py-2 text-white">Edit service</a>
            @endcan
        </div>
    </div>
    <div class="grid gap-6 lg:grid-cols-2">
        <section class="rounded-3xl bg-white p-6">
            <h2 class="text-xs uppercase tracking-[0.2em] opacity-50">Included</h2>
            <ul class="mt-4 space-y-2 text-sm">
                @forelse ($package->items as $item)
                    <li>{{ $item->name }}</li>
                @empty
                    <li class="opacity-50">No line items yet.</li>
                @endforelse
            </ul>
        </section>
        <section class="rounded-3xl bg-white p-6">
            <h2 class="text-xs uppercase tracking-[0.2em] opacity-50">Coverage</h2>
            <ul class="mt-4 space-y-2 text-sm">
                <li>{{ $package->duration_hours }} hours</li>
                <li>{{ $package->photographer_count }} photographers · {{ $package->videographer_count }} videographers</li>
                <li>{{ $package->edited_photos }} edited photos</li>
                @forelse ($package->activeIncludes() as $include)
                    <li>{{ $include }}</li>
                @empty
                    <li class="opacity-50">No includes selected.</li>
                @endforelse
                <li>YouTube: {{ $package->hasYoutubeVideo() ? 'Linked' : 'Not set' }}</li>
            </ul>
        </section>
    </div>
    @if ($package->hasYoutubeVideo())
        <section class="rounded-3xl bg-white p-6">
            <h2 class="text-xs uppercase tracking-[0.2em] opacity-50">Service video</h2>
            <x-youtube-embed :url="$package->youtube_url" :title="$package->name" class="mt-4 max-w-2xl" />
        </section>
    @endif
    <section class="rounded-3xl bg-white p-6">
        <h2 class="text-xs uppercase tracking-[0.2em] opacity-50">Projects using this service</h2>
        <ul class="mt-4 divide-y divide-[#16120f]/5 text-sm">
            @forelse ($projects as $project)
                <li class="flex items-center justify-between py-3">
                    <a href="{{ route('app.projects.show', $project) }}" class="font-medium">{{ $project->title }}</a>
                    <span class="opacity-50">{{ $project->customer?->name }}</span>
                </li>
            @empty
                <li class="py-6 opacity-50">No booked projects on this package yet.</li>
            @endforelse
        </ul>
    </section>
</div>
