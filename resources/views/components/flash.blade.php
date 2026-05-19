@if (session('success'))
    <div {{ $attributes->merge(['class' => 'rounded-clay-lg border border-emerald-200/80 bg-emerald-50/90 px-4 py-3 text-sm font-medium text-emerald-900 shadow-sm backdrop-blur-sm']) }}>
        {{ session('success') }}
    </div>
@endif

@if (session('error'))
    <div class="rounded-clay-lg border border-red-200/80 bg-red-50/90 px-4 py-3 text-sm font-medium text-red-900 shadow-sm backdrop-blur-sm">
        {{ session('error') }}
    </div>
@endif
