<div>
    <div class="mb-6 flex flex-wrap items-end justify-between gap-4">
        <div>
            <h1 class="font-[Cormorant_Garamond] text-3xl text-[#16120f] md:text-4xl">Website CMS</h1>
            <p class="mt-1 text-sm text-[#16120f]/60">Edit public pages, media, and studio content from one place.</p>
        </div>
        <a href="/" target="_blank" rel="noopener noreferrer" class="inline-flex rounded-full bg-white px-4 py-2 text-sm font-medium shadow-sm ring-1 ring-black/5 transition hover:bg-[#f6f1ea]">
            View public site
        </a>
    </div>

    @if (session('status'))
        <p class="mb-6 rounded-2xl bg-emerald-50 px-4 py-3 text-sm text-emerald-800 ring-1 ring-emerald-200">{{ session('status') }}</p>
    @endif

    <div class="lg:grid lg:grid-cols-12 lg:gap-8">
        <aside class="mb-8 lg:col-span-3 lg:mb-0">
            <nav class="sticky top-24 space-y-1 rounded-[28px] bg-white p-3 shadow-sm ring-1 ring-black/5" aria-label="Website CMS menu">
                @foreach ($cmsMenu as $item)
                    @if (! empty($item['children']))
                        <div class="pt-1">
                            <p class="px-3 pb-1.5 pt-2 text-[10px] font-semibold uppercase tracking-[0.2em] text-[#9b7b4b]">{{ $item['label'] }}</p>
                            <ul class="space-y-0.5">
                                @foreach ($item['children'] as $child)
                                    <li>
                                        <button
                                            type="button"
                                            wire:click="selectMenu('content', '{{ $child['id'] }}')"
                                            class="flex w-full rounded-xl px-3 py-2.5 text-left text-sm transition {{ $menu === 'content' && $copyGroup === $child['id'] ? 'bg-[#16120f] font-medium text-white' : 'text-[#16120f]/75 hover:bg-[#f6f1ea]' }}"
                                        >
                                            {{ $child['label'] }}
                                        </button>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @else
                        <button
                            type="button"
                            wire:click="selectMenu('{{ $item['id'] }}')"
                            class="flex w-full rounded-xl px-3 py-2.5 text-left text-sm font-medium transition {{ $menu === $item['id'] ? 'bg-[#16120f] text-white' : 'text-[#16120f]/75 hover:bg-[#f6f1ea]' }}"
                        >
                            {{ $item['label'] }}
                        </button>
                    @endif
                @endforeach
            </nav>
        </aside>

        <div class="min-w-0 lg:col-span-9">
            @if ($menu === 'content')
                <form wire:submit="saveCopy" class="space-y-6">
                    <header class="rounded-[28px] bg-white px-6 py-5 shadow-sm ring-1 ring-black/5">
                        <p class="text-[11px] font-semibold uppercase tracking-[0.22em] text-[#9b7b4b]">Page content</p>
                        <h2 class="mt-1 font-[Cormorant_Garamond] text-2xl md:text-3xl">{{ $copyGroupLabel }}</h2>
                        <p class="mt-2 text-sm text-[#16120f]/60">Changes apply to the live website after you save.</p>
                    </header>

                    @if ($activeCopyRows->isEmpty())
                        <p class="rounded-[28px] bg-white p-8 text-sm text-[#16120f]/60 shadow-sm ring-1 ring-black/5">No editable fields in this section yet.</p>
                    @else
                        <section class="rounded-[28px] bg-white p-6 shadow-sm ring-1 ring-black/5">
                            <div class="grid gap-4">
                                @foreach ($activeCopyRows as $row)
                                    <label class="block text-sm">
                                        <span class="text-[#16120f]/55">{{ $row->label ?: $row->key }}</span>
                                        @if ($row->type === 'input')
                                            <input class="mt-1.5 w-full rounded-2xl bg-[#f6f1ea] px-4 py-3" wire:model="fields.{{ $row->key }}">
                                        @else
                                            <x-studio.rich-textarea
                                                :model="'fields.'.$row->key"
                                                :rows="strlen((string) $row->value) > 180 ? 6 : 3"
                                                class="mt-1.5"
                                            />
                                        @endif
                                    </label>
                                @endforeach
                            </div>
                        </section>
                    @endif

                    <button type="submit" class="rounded-full bg-[#16120f] px-6 py-3 text-sm font-medium text-white shadow-sm">Save {{ $copyGroupLabel }}</button>
                </form>
            @endif

            @if ($menu === 'social')
                <header class="mb-6 rounded-[28px] bg-white px-6 py-5 shadow-sm ring-1 ring-black/5">
                    <p class="text-[11px] font-semibold uppercase tracking-[0.22em] text-[#9b7b4b]">Connect</p>
                    <h2 class="mt-1 font-[Cormorant_Garamond] text-2xl md:text-3xl">Social media</h2>
                    <p class="mt-2 text-sm text-[#16120f]/60">Only platforms with a link appear in the footer and on the contact page.</p>
                </header>
                <form wire:submit="saveSocial" class="space-y-6">
                    <div class="grid gap-4 md:grid-cols-2">
                        @foreach (\App\Support\SocialPlatforms::definitions() as $def)
                            <label class="block rounded-[28px] bg-white p-5 shadow-sm ring-1 ring-black/5">
                                <span class="flex items-center gap-2 text-sm font-medium">
                                    @include('components.partials.social-icon', ['platform' => $def['id'], 'class' => 'h-4 w-4 opacity-70'])
                                    {{ $def['label'] }}
                                </span>
                                <input
                                    type="url"
                                    inputmode="url"
                                    class="mt-2 w-full rounded-2xl bg-[#f6f1ea] px-4 py-3 text-sm"
                                    placeholder="{{ $def['placeholder'] }}"
                                    wire:model="fields.{{ $def['key'] }}"
                                >
                                @error('fields.'.$def['key'])
                                    <p class="mt-2 text-xs text-rose-700">{{ $message }}</p>
                                @enderror
                            </label>
                        @endforeach
                    </div>
                    <button type="submit" class="rounded-full bg-[#16120f] px-6 py-3 text-sm font-medium text-white">Save social links</button>
                </form>
            @endif

            @if ($menu === 'team')
                <header class="mb-6 rounded-[28px] bg-white px-6 py-5 shadow-sm ring-1 ring-black/5">
                    <p class="text-[11px] font-semibold uppercase tracking-[0.22em] text-[#9b7b4b]">People</p>
                    <h2 class="mt-1 font-[Cormorant_Garamond] text-2xl md:text-3xl">Our team</h2>
                </header>
                <form wire:submit="addTeamMember" class="mb-6 grid gap-3 rounded-[28px] bg-white p-6 shadow-sm ring-1 ring-black/5 md:grid-cols-3">
                    <input wire:model="team_name" placeholder="Name" class="rounded-2xl bg-[#f6f1ea] px-4 py-3">
                    <input wire:model="team_role" placeholder="Role" class="rounded-2xl bg-[#f6f1ea] px-4 py-3">
                    <button class="rounded-full bg-[#16120f] text-white">Add team member</button>
                </form>
                <div class="grid gap-3 md:grid-cols-2">
                    @foreach ($team as $member)
                        <article class="flex items-center justify-between rounded-[28px] bg-white p-5 shadow-sm ring-1 ring-black/5">
                            <div><p class="font-medium">{{ $member->name }}</p><p class="text-sm opacity-60">{{ $member->role }}</p></div>
                            <button type="button" wire:click="deleteTeamMember({{ $member->id }})" class="text-sm text-rose-700">Remove</button>
                        </article>
                    @endforeach
                </div>
            @endif

            @if ($menu === 'testimonials')
                <header class="mb-6 rounded-[28px] bg-white px-6 py-5 shadow-sm ring-1 ring-black/5">
                    <p class="text-[11px] font-semibold uppercase tracking-[0.22em] text-[#9b7b4b]">Social proof</p>
                    <h2 class="mt-1 font-[Cormorant_Garamond] text-2xl md:text-3xl">Testimonials</h2>
                </header>
                <form wire:submit="addTestimonial" class="mb-6 grid gap-3 rounded-[28px] bg-white p-6 shadow-sm ring-1 ring-black/5">
                    <input wire:model="testimonial_author" placeholder="Author" class="rounded-2xl bg-[#f6f1ea] px-4 py-3">
                    <input wire:model="testimonial_role" placeholder="Role" class="rounded-2xl bg-[#f6f1ea] px-4 py-3">
                    <x-studio.rich-textarea model="testimonial_quote" placeholder="Quote" :rows="4" />
                    <button class="rounded-full bg-[#16120f] py-3 text-white">Add testimonial</button>
                </form>
                <div class="space-y-3">
                    @foreach ($testimonials as $item)
                        <article class="rounded-[28px] bg-white p-5 shadow-sm ring-1 ring-black/5">
                            <p class="font-[Cormorant_Garamond] text-xl">“{{ $item->quote }}”</p>
                            <p class="mt-2 text-sm opacity-60">{{ $item->author }} · {{ $item->role }}</p>
                            <button type="button" wire:click="deleteTestimonial({{ $item->id }})" class="mt-2 text-sm text-rose-700">Remove</button>
                        </article>
                    @endforeach
                </div>
            @endif

            @if ($menu === 'portfolio')
                <header class="mb-6 rounded-[28px] bg-white px-6 py-5 shadow-sm ring-1 ring-black/5">
                    <p class="text-[11px] font-semibold uppercase tracking-[0.22em] text-[#9b7b4b]">Media</p>
                    <h2 class="mt-1 font-[Cormorant_Garamond] text-2xl md:text-3xl">Gallery images</h2>
                </header>
                <form wire:submit="addPortfolio" class="mb-6 grid gap-3 rounded-[28px] bg-white p-6 shadow-sm ring-1 ring-black/5">
                    <input wire:model="portfolio_title" placeholder="Title" class="rounded-2xl bg-[#f6f1ea] px-4 py-3">
                    <input wire:model="portfolio_image" placeholder="Image URL" class="rounded-2xl bg-[#f6f1ea] px-4 py-3">
                    <input wire:model="portfolio_category" placeholder="Category" class="rounded-2xl bg-[#f6f1ea] px-4 py-3">
                    <button class="rounded-full bg-[#16120f] py-3 text-white">Add gallery image</button>
                </form>
                <div class="grid grid-cols-2 gap-3 md:grid-cols-4">
                    @foreach ($portfolio as $item)
                        <figure class="relative rounded-[28px] bg-white p-2 shadow-sm ring-1 ring-black/5">
                            <img src="{{ $item->image_path }}" alt="{{ $item->title }}" class="h-40 w-full rounded-2xl object-cover">
                            <button type="button" wire:click="deletePortfolio({{ $item->id }})" class="absolute right-4 top-4 rounded-full bg-white px-2 py-0.5 text-xs shadow">Remove</button>
                            <figcaption class="mt-2 px-1 text-xs">{{ $item->title }}</figcaption>
                            @if ($item->slug)
                                <a href="{{ $item->publicUrl() }}" class="px-1 text-xs text-[#9b7b4b]" target="_blank" rel="noopener noreferrer">Public page</a>
                            @endif
                        </figure>
                    @endforeach
                </div>
            @endif

            @if ($menu === 'faq')
                <header class="mb-6 rounded-[28px] bg-white px-6 py-5 shadow-sm ring-1 ring-black/5">
                    <p class="text-[11px] font-semibold uppercase tracking-[0.22em] text-[#9b7b4b]">Help</p>
                    <h2 class="mt-1 font-[Cormorant_Garamond] text-2xl md:text-3xl">FAQ</h2>
                </header>
                <form wire:submit="addFaq" class="mb-6 grid gap-3 rounded-[28px] bg-white p-6 shadow-sm ring-1 ring-black/5">
                    <input wire:model="faq_question" placeholder="Question" class="rounded-2xl bg-[#f6f1ea] px-4 py-3">
                    <x-studio.rich-textarea model="faq_answer" placeholder="Answer" :rows="5" />
                    <button class="rounded-full bg-[#16120f] py-3 text-white">Add FAQ</button>
                </form>
                <div class="space-y-3">
                    @foreach ($faqs as $faq)
                        <details class="rounded-[28px] bg-white p-5 shadow-sm ring-1 ring-black/5">
                            <summary class="cursor-pointer font-medium">{{ $faq->question }}</summary>
                            <p class="mt-2 text-sm opacity-70">{{ $faq->answer }}</p>
                            <button type="button" wire:click="deleteFaq({{ $faq->id }})" class="mt-2 text-sm text-rose-700">Remove</button>
                        </details>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div>
