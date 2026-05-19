<header class="sticky top-0 z-30 flex h-16 shrink-0 items-center gap-4 border-b border-clay-hairline/70 bg-clay-canvas/75 px-4 backdrop-blur-md lg:px-8">
    <button
        type="button"
        class="inline-flex rounded-clay border border-clay-hairline bg-white/80 p-2 text-clay-ink shadow-sm hover:bg-clay-surface-soft lg:hidden"
        @click="navOpen = true"
        aria-label="{{ __('Open menu') }}"
    >
        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
    </button>

    <div class="hidden min-w-0 flex-1 lg:block">
        <p class="truncate text-xs font-semibold uppercase tracking-widest text-clay-muted">{{ __('Workspace') }}</p>
        <p class="truncate text-sm font-medium text-clay-body-strong">{{ Auth::user()->name }}</p>
    </div>

    <div class="ms-auto flex items-center gap-2">
        <x-dropdown align="right" width="48" contentClasses="py-1 rounded-clay border border-clay-hairline bg-white/95 shadow-clay-card backdrop-blur-md">
            <x-slot name="trigger">
                <button type="button" class="inline-flex items-center gap-2 rounded-full border border-clay-hairline bg-white/90 px-3 py-1.5 text-sm font-medium text-clay-ink shadow-sm hover:bg-white">
                    <span class="max-w-[10rem] truncate">{{ Auth::user()->name }}</span>
                    <svg class="h-4 w-4 text-clay-muted" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                    </svg>
                </button>
            </x-slot>
            <x-slot name="content">
                <x-dropdown-link :href="route('profile.edit')" class="text-clay-body">
                    {{ __('Profile') }}
                </x-dropdown-link>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <x-dropdown-link :href="route('logout')" onclick="event.preventDefault(); this.closest('form').submit();" class="text-clay-body">
                        {{ __('Log Out') }}
                    </x-dropdown-link>
                </form>
            </x-slot>
        </x-dropdown>
    </div>
</header>
