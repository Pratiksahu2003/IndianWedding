<nav class="mb-8 flex flex-wrap gap-2 text-sm">
    <a href="{{ route('app.settings.index') }}" class="rounded-full px-4 py-2 {{ request()->routeIs('app.settings.index') ? 'bg-[#16120f] text-white' : 'bg-white text-[#16120f]/70 ring-1 ring-black/5' }}">Studio profile</a>
    <a href="{{ route('app.settings.payments') }}" class="rounded-full px-4 py-2 {{ request()->routeIs('app.settings.payments') ? 'bg-[#16120f] text-white' : 'bg-white text-[#16120f]/70 ring-1 ring-black/5' }}">Payment gateway</a>
    <a href="{{ route('app.settings.email') }}" class="rounded-full px-4 py-2 {{ request()->routeIs('app.settings.email') ? 'bg-[#16120f] text-white' : 'bg-white text-[#16120f]/70 ring-1 ring-black/5' }}">Email / SMTP</a>
    <a href="{{ route('app.settings.google-drive') }}" class="rounded-full px-4 py-2 {{ request()->routeIs('app.settings.google-drive') ? 'bg-[#16120f] text-white' : 'bg-white text-[#16120f]/70 ring-1 ring-black/5' }}">Google Drive</a>
</nav>
