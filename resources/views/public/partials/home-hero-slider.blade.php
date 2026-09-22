@php
    use App\Support\UnikStudioAssets;

    $slides = [
        [
            'num' => '01',
            'tab' => site('home.slide_1_tab', 'Wedding Photography & Films'),
            'title' => site('home.slide_1_title', 'Wedding Photography & Films'),
            'description' => site('home.slide_1_body', 'Two-camera coverage of rituals, family emotions and reception nights — crafted into albums and films you will keep forever.'),
            'cta' => site('home.slide_1_cta', 'View weddings'),
            'href' => site('home.slide_1_href', '/services/wedding-photography'),
            'image' => site('home.slide_1_image') ?: UnikStudioAssets::url('service-wedding.jpg'),
        ],
        [
            'num' => '02',
            'tab' => site('home.slide_2_tab', 'Pre-Wedding Stories'),
            'title' => site('home.slide_2_title', 'Pre-Wedding Stories'),
            'description' => site('home.slide_2_body', 'Golden-hour portraits and cinematic outdoors — a romantic chapter before the vows, styled for print and film.'),
            'cta' => site('home.slide_2_cta', 'See pre-weddings'),
            'href' => site('home.slide_2_href', '/services/pre-wedding-shoot'),
            'image' => site('home.slide_2_image') ?: UnikStudioAssets::url('service-prewedding.jpg'),
        ],
        [
            'num' => '03',
            'tab' => site('home.slide_3_tab', 'Candid Documentation'),
            'title' => site('home.slide_3_title', 'Candid Documentation'),
            'description' => site('home.slide_3_body', 'Unposed laughter, quiet glances and real energy between rituals — photography that feels honest, never staged.'),
            'cta' => site('home.slide_3_cta', 'Explore candid'),
            'href' => site('home.slide_3_href', '/services/candid-shoot'),
            'image' => site('home.slide_3_image') ?: UnikStudioAssets::url('service-candid.jpg'),
        ],
        [
            'num' => '04',
            'tab' => site('home.slide_4_tab', 'Cinematography'),
            'title' => site('home.slide_4_title', 'Cinematography & Films'),
            'description' => site('home.slide_4_body', 'Movie-language storytelling with colour, motion and sound — highlight films and teaser reels for your wedding day.'),
            'cta' => site('home.slide_4_cta', 'Watch films'),
            'href' => site('home.slide_4_href', '/services/cinematography'),
            'image' => site('home.slide_4_image') ?: UnikStudioAssets::url('service-cinema.jpg'),
        ],
        [
            'num' => '05',
            'tab' => site('home.slide_5_tab', 'Birthdays & Music Videos'),
            'title' => site('home.slide_5_title', 'Birthdays & Music Videos'),
            'description' => site('home.slide_5_body', 'Celebrations and creative productions beyond weddings — birthdays, music videos and lifestyle shoots with the same craft.'),
            'cta' => site('home.slide_5_cta', 'Book a shoot'),
            'href' => site('home.slide_5_href', '/book-consultation'),
            'image' => site('home.slide_5_image') ?: UnikStudioAssets::url('service-birthday.jpg'),
        ],
    ];
@endphp

<section
    class="home-hero-slider relative isolate -mt-[68px] min-h-[100svh] overflow-hidden bg-[#0c0b0a] text-white"
    x-data="homeHeroSlider({ total: {{ count($slides) }}, duration: 6000 })"
    x-init="start()"
    @mouseenter="pause()"
    @mouseleave="resume()"
>
    <div class="absolute inset-0">
        @foreach ($slides as $i => $slide)
            <div
                class="absolute inset-0 transition-opacity duration-700 ease-out"
                :class="active === {{ $i }} ? 'opacity-100 z-[1]' : 'opacity-0 z-0'"
            >
                <img
                    src="{{ $slide['image'] }}"
                    alt="{{ $slide['title'] }}"
                    class="h-full w-full object-cover"
                    @if ($i === 0) fetchpriority="high" @else loading="lazy" @endif
                >
                <div class="absolute inset-0 bg-gradient-to-r from-[#0c0b0a]/92 via-[#0c0b0a]/55 to-[#0c0b0a]/20"></div>
                <div class="absolute inset-0 bg-gradient-to-t from-[#0c0b0a] via-transparent to-[#0c0b0a]/40"></div>
            </div>
        @endforeach
    </div>

    <div class="relative z-10 flex min-h-[100svh] flex-col justify-end pb-44 pt-28 sm:pb-40 md:pb-44 md:pt-32">
        <div class="mx-auto w-full max-w-6xl px-5 md:px-8">
            @foreach ($slides as $i => $slide)
                <div
                    x-show="active === {{ $i }}"
                    x-transition:enter="transition ease-out duration-500"
                    x-transition:enter-start="opacity-0 translate-y-5"
                    x-transition:enter-end="opacity-100 translate-y-0"
                    x-transition:leave="transition ease-in duration-200 absolute"
                    x-transition:leave-start="opacity-100"
                    x-transition:leave-end="opacity-0"
                    class="max-w-2xl"
                    @unless ($i === 0) x-cloak @endunless
                >
                    <p class="text-sm font-semibold tracking-[0.22em] text-[#c4a574]">{{ $slide['num'] }}</p>
                    <h1 class="mt-3 font-[Cormorant_Garamond] text-[2.35rem] leading-[1.08] text-balance text-white sm:text-5xl md:text-6xl lg:text-7xl">
                        {{ $slide['title'] }}
                    </h1>
                    <p class="mt-5 max-w-xl text-[15px] leading-relaxed text-white/78 sm:text-lg">
                        {{ $slide['description'] }}
                    </p>
                    <div class="mt-8">
                        <a
                            href="{{ $slide['href'] }}"
                            class="inline-flex items-center rounded-sm bg-[#c4a574] px-6 py-3 text-sm font-semibold text-[#16120f] transition hover:bg-[#d4b888]"
                        >
                            {{ $slide['cta'] }}
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <nav class="absolute inset-x-0 bottom-0 z-20 border-t border-white/10 bg-[#0c0b0a]/80 backdrop-blur-md" aria-label="Hero slides">
        <div class="mx-auto grid max-w-6xl grid-cols-5 divide-x divide-white/10">
            @foreach ($slides as $i => $slide)
                <button
                    type="button"
                    class="group relative min-w-0 px-2 py-3.5 text-left transition sm:px-3 md:px-5 md:py-5"
                    :class="active === {{ $i }} ? 'bg-white/[0.06]' : 'hover:bg-white/[0.03]'"
                    @click="go({{ $i }})"
                    :aria-current="active === {{ $i }} ? 'true' : null"
                >
                    <span
                        class="pointer-events-none absolute inset-x-0 top-0 h-[2px] origin-left scale-x-0 bg-[#c4a574]"
                        x-ref="bar{{ $i }}"
                    ></span>
                    <span
                        class="block text-[10px] font-semibold tracking-[0.12em] sm:text-[11px] sm:tracking-[0.14em]"
                        :class="active === {{ $i }} ? 'text-[#c4a574]' : 'text-white/35'"
                    >{{ $slide['num'] }}</span>
                    <span
                        class="mt-1 block truncate text-[10px] leading-snug sm:mt-1.5 sm:whitespace-normal sm:text-xs md:text-[13px]"
                        :class="active === {{ $i }} ? 'font-semibold text-white' : 'text-white/45'"
                    >{{ $slide['tab'] }}</span>
                </button>
            @endforeach
        </div>
    </nav>
</section>
