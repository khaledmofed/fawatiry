<aside
    class="fixed inset-y-0 start-0 z-50 flex w-72 max-w-[85vw] flex-col clay-glass-dark transition-transform duration-200 ease-out lg:translate-x-0"
    :class="{ 'translate-x-0': navOpen, '-translate-x-full': !navOpen }"
    aria-label="{{ __('Main navigation') }}"
>
    <div class="flex h-16 items-center gap-2 border-b border-white/10 px-5">
        <span class="text-lg font-semibold tracking-tight text-white">{{ config('app.name') }}</span>
    </div>

    <nav class="flex-1 space-y-1 overflow-y-auto px-3 py-4">
        <x-clay.sidebar-link :href="route('dashboard')" :active="request()->routeIs('dashboard')" icon="◆">
            {{ __('Dashboard') }}
        </x-clay.sidebar-link>
        <x-clay.sidebar-link :href="route('invoices.index')" :active="request()->routeIs('invoices.*')" icon="◇">
            {{ __('Invoices') }}
        </x-clay.sidebar-link>
        <x-clay.sidebar-link :href="route('invoice-templates.index')" :active="request()->routeIs('invoice-templates.*')" icon="◈">
            {{ __('Templates') }}
        </x-clay.sidebar-link>
        <x-clay.sidebar-link :href="route('clients.index')" :active="request()->routeIs('clients.*')" icon="◎">
            {{ __('Clients') }}
        </x-clay.sidebar-link>
        <x-clay.sidebar-link :href="route('products.index')" :active="request()->routeIs('products.*')" icon="▣">
            {{ __('Products') }}
        </x-clay.sidebar-link>
        <x-clay.sidebar-link :href="route('media.index')" :active="request()->routeIs('media.*')" icon="▤">
            {{ __('Media') }}
        </x-clay.sidebar-link>
        <x-clay.sidebar-link :href="route('settings.edit')" :active="request()->routeIs('settings.*')" icon="⚙">
            {{ __('Company') }}
        </x-clay.sidebar-link>
    </nav>

    <div class="border-t border-white/10 p-4 text-xs text-clay-on-dark-soft">
        {{ __('Clay-inspired UI · Blade + Tailwind') }}
    </div>
</aside>
