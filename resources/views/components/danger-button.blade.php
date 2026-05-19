<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex h-11 items-center justify-center rounded-clay bg-clay-error px-5 text-sm font-semibold text-white shadow-sm transition hover:bg-red-600 focus:outline-none focus:ring-2 focus:ring-red-400/40 focus:ring-offset-2 focus:ring-offset-clay-canvas disabled:opacity-40']) }}>
    {{ $slot }}
</button>
