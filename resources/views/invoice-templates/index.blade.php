<x-app-layout>
    <x-slot name="header">
        <x-clay.page-header :title="__('Choose a template')" :subtitle="__('Pick a layout to start your invoice. You can change everything in the editor.')" />
    </x-slot>

    <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
        @foreach ($templates as $template)
            @php
                $previewSlug = \App\Support\InvoiceLayoutDocument::resolvePreviewSlug($template->slug);
                $theme = \App\Support\InvoicePreviewThemes::for($previewSlug);
            @endphp
            <x-clay.card class="group !p-0 overflow-hidden transition hover:shadow-clay-card" :glass="true">
                <div class="flex min-h-[300px] items-start justify-center overflow-hidden bg-clay-surface-soft sm:min-h-[320px] lg:min-h-[340px]">
                    @include('invoice-templates.partials.mini-preview', ['slug' => $previewSlug, 'theme' => $theme, 'direction' => $template->direction])
                </div>
                <div class="space-y-4 p-6">
                    <div>
                        <h2 class="text-lg font-semibold tracking-tight text-clay-ink">{{ $template->name }}</h2>
                        <p class="mt-1 text-xs font-semibold uppercase tracking-wider text-clay-muted">{{ strtoupper($template->direction) }}</p>
                    </div>
                    <form method="post" action="{{ route('invoices.store') }}">
                        @csrf
                        <input type="hidden" name="invoice_template_id" value="{{ $template->id }}">
                        <x-primary-button class="w-full justify-center">{{ __('Use template') }}</x-primary-button>
                    </form>
                </div>
            </x-clay.card>
        @endforeach
    </div>
</x-app-layout>
