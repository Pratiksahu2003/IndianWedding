<div>
    <div class="mb-6 flex flex-wrap gap-2 text-sm">
        @foreach (['copy'=>'All website words','team'=>'Our team','testimonials'=>'Testimonials','portfolio'=>'Gallery','faq'=>'FAQ'] as $id=>$label)
            <button type="button" wire:click="$set('tab', '{{ $id }}')" class="rounded-full px-4 py-2 {{ $tab === $id ? 'bg-[#16120f] text-white' : 'bg-white' }}">{{ $label }}</button>
        @endforeach
        <a href="/" target="_blank" class="rounded-full bg-white px-4 py-2">View public site</a>
    </div>

    @if ($tab === 'copy')
        <form wire:submit="saveCopy" class="space-y-8">
            <p class="text-sm opacity-70">Every heading, sentence and contact line from Unik Studio lives here. Change a field and save — the public pages update immediately.</p>
            @foreach ($groups as $group => $rows)
                <section class="rounded-3xl bg-white p-6">
                    <h2 class="mb-4 font-[Cormorant_Garamond] text-2xl capitalize">{{ str_replace('_', ' ', $group) }}</h2>
                    <div class="grid gap-4">
                        @foreach ($rows as $row)
                            <label class="block text-sm">
                                <span class="opacity-60">{{ $row->label ?: $row->key }}</span>
                                @if ($row->type === 'input')
                                    <input class="mt-1 w-full rounded-2xl bg-[#f6f1ea] px-4 py-3" wire:model="fields.{{ $row->key }}">
                                @else
                                    <textarea rows="{{ strlen((string) $row->value) > 180 ? 6 : 3 }}" class="mt-1 w-full rounded-2xl bg-[#f6f1ea] px-4 py-3" wire:model="fields.{{ $row->key }}"></textarea>
                                @endif
                            </label>
                        @endforeach
                    </div>
                </section>
            @endforeach
            <button class="rounded-full bg-[#16120f] px-6 py-3 text-white">Save all copy</button>
        </form>
    @endif

    @if ($tab === 'team')
        <form wire:submit="addTeamMember" class="mb-6 grid gap-3 rounded-3xl bg-white p-6 md:grid-cols-3">
            <input wire:model="team_name" placeholder="Name" class="rounded-2xl bg-[#f6f1ea] px-4 py-3">
            <input wire:model="team_role" placeholder="Role" class="rounded-2xl bg-[#f6f1ea] px-4 py-3">
            <button class="rounded-full bg-[#16120f] text-white">Add team member</button>
        </form>
        <div class="grid gap-3 md:grid-cols-2">
            @foreach ($team as $member)
                <article class="flex items-center justify-between rounded-3xl bg-white p-5">
                    <div><p class="font-medium">{{ $member->name }}</p><p class="text-sm opacity-60">{{ $member->role }}</p></div>
                    <button wire:click="deleteTeamMember({{ $member->id }})" class="text-sm text-rose-700">Remove</button>
                </article>
            @endforeach
        </div>
    @endif

    @if ($tab === 'testimonials')
        <form wire:submit="addTestimonial" class="mb-6 grid gap-3 rounded-3xl bg-white p-6">
            <input wire:model="testimonial_author" placeholder="Author" class="rounded-2xl bg-[#f6f1ea] px-4 py-3">
            <input wire:model="testimonial_role" placeholder="Role" class="rounded-2xl bg-[#f6f1ea] px-4 py-3">
            <textarea wire:model="testimonial_quote" placeholder="Quote" class="rounded-2xl bg-[#f6f1ea] px-4 py-3"></textarea>
            <button class="rounded-full bg-[#16120f] py-3 text-white">Add testimonial</button>
        </form>
        <div class="space-y-3">
            @foreach ($testimonials as $item)
                <article class="rounded-3xl bg-white p-5">
                    <p class="font-[Cormorant_Garamond] text-xl">“{{ $item->quote }}”</p>
                    <p class="mt-2 text-sm opacity-60">{{ $item->author }} · {{ $item->role }}</p>
                    <button wire:click="deleteTestimonial({{ $item->id }})" class="mt-2 text-sm text-rose-700">Remove</button>
                </article>
            @endforeach
        </div>
    @endif

    @if ($tab === 'portfolio')
        <form wire:submit="addPortfolio" class="mb-6 grid gap-3 rounded-3xl bg-white p-6">
            <input wire:model="portfolio_title" placeholder="Title" class="rounded-2xl bg-[#f6f1ea] px-4 py-3">
            <input wire:model="portfolio_image" placeholder="Image URL" class="rounded-2xl bg-[#f6f1ea] px-4 py-3">
            <input wire:model="portfolio_category" placeholder="Category" class="rounded-2xl bg-[#f6f1ea] px-4 py-3">
            <button class="rounded-full bg-[#16120f] py-3 text-white">Add gallery image</button>
        </form>
        <div class="grid grid-cols-2 gap-3 md:grid-cols-4">
            @foreach ($portfolio as $item)
                <figure class="relative">
                    <img src="{{ $item->image_path }}" alt="{{ $item->title }}" class="h-40 w-full rounded-2xl object-cover">
                    <button wire:click="deletePortfolio({{ $item->id }})" class="absolute right-2 top-2 rounded-full bg-white px-2 text-xs">Remove</button>
                    <figcaption class="mt-1 text-xs">{{ $item->title }}</figcaption>
                    @if ($item->slug)
                        <a href="{{ $item->publicUrl() }}" class="text-xs text-[#9b7b4b]" target="_blank">Public page</a>
                    @endif
                </figure>
            @endforeach
        </div>
    @endif

    @if ($tab === 'faq')
        <form wire:submit="addFaq" class="mb-6 grid gap-3 rounded-3xl bg-white p-6">
            <input wire:model="faq_question" placeholder="Question" class="rounded-2xl bg-[#f6f1ea] px-4 py-3">
            <textarea wire:model="faq_answer" placeholder="Answer" class="rounded-2xl bg-[#f6f1ea] px-4 py-3"></textarea>
            <button class="rounded-full bg-[#16120f] py-3 text-white">Add FAQ</button>
        </form>
        <div class="space-y-3">
            @foreach ($faqs as $faq)
                <details class="rounded-3xl bg-white p-5">
                    <summary class="cursor-pointer font-medium">{{ $faq->question }}</summary>
                    <p class="mt-2 text-sm opacity-70">{{ $faq->answer }}</p>
                    <button wire:click="deleteFaq({{ $faq->id }})" class="mt-2 text-sm text-rose-700">Remove</button>
                </details>
            @endforeach
        </div>
    @endif
</div>
