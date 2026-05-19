@php
    /** @var array<string, string> $theme */
    $t = $theme;
    $heroDark = in_array($themeSlug ?? '', ['corporate-blue', 'creative-agency', 'luxury-black-gold', 'dark-modern', 'premium-gold'], true);
    $pc = \App\Support\InvoicePreviewThemes::pdfColors($themeSlug ?? 'clean-white');
@endphp

<div class="{{ $t['wrap'] }} px-6 py-8 sm:px-10 sm:py-10" style="{{ $pc['wrapStyle'] }}" :dir="invoice.direction">

    {{-- TOP BAR: logo + invoice title + number/status --}}
    <div class="{{ $t['topBar'] }}">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
            <div class="flex min-w-0 flex-1 items-start gap-4">
                <div class="relative flex h-[4.5rem] w-[4.5rem] shrink-0 items-center justify-center overflow-hidden rounded-xl border border-black/10 bg-white/90 shadow-sm ring-1 ring-black/5">
                    <template x-if="company.logo_url">
                        <img
                            :src="company.logo_url"
                            alt=""
                            class="max-h-full max-w-full object-contain"
                            :style="logoImageStyle()"
                            draggable="false"
                        />
                    </template>
                    <template x-if="!company.logo_url">
                        <span class="px-2 text-center text-[10px] font-semibold uppercase leading-tight text-slate-400">Logo</span>
                    </template>
                    <div
                        class="absolute bottom-0.5 end-0.5 cursor-grab rounded bg-black/55 px-1 text-[9px] font-bold text-white shadow"
                        title="{{ __('Nudge logo position') }}"
                        @pointerdown.stop="logoPointerDown($event)"
                    >⇄</div>
                    <input type="file" x-ref="logoFile" class="hidden" accept="image/png,image/jpeg,image/webp,image/gif" @change="uploadLogoFile($event)" />
                </div>
                <div class="min-w-0">
                    <p class="mb-1 text-[11px] font-semibold uppercase tracking-wider {{ $heroDark ? 'text-white/70' : $t['sectionTitle'] }}">{{ __('Invoice') }}</p>
                    <h1 class="{{ $t['title'] }}" x-text="strings.invoice"></h1>
                </div>
            </div>
            <div class="text-start sm:text-end {{ $heroDark ? 'text-white' : '' }}">
                <p class="text-sm opacity-80">#<span x-text="invoice.number" class="font-mono font-semibold"></span></p>
                <div class="mt-2 inline-flex">
                    <span class="{{ $t['badgeWrap'] }}" :class="badgeVariantClass()" x-text="statusLabel()"></span>
                </div>
            </div>
        </div>
    </div>

    {{-- TWO COLUMNS: From + Invoice Details | Bill To --}}
    <div class="mt-8 grid gap-6 lg:grid-cols-2">
        <div class="space-y-4">
            {{-- From --}}
            <div>
                <p class="{{ $t['sectionTitle'] }} mb-2">{{ __('From') }}</p>
                <div class="min-w-0 space-y-1 text-sm">
                    <p class="text-base font-bold leading-snug" x-text="company.name"></p>
                    <p class="whitespace-pre-line opacity-90" x-text="company.address || '—'"></p>
                    <p class="opacity-90" x-show="company.phone" x-text="company.phone"></p>
                    <p class="opacity-90" x-show="company.email" x-text="company.email"></p>
                    <p class="text-xs opacity-75" x-show="company.vat_number">{{ __('VAT') }}: <span x-text="company.vat_number"></span></p>
                </div>
            </div>
            {{-- Invoice details --}}
            <div>
                <p class="{{ $t['sectionTitle'] }} mb-2">{{ __('Invoice details') }}</p>
                <dl class="grid grid-cols-1 gap-2 text-sm">
                    <div class="flex flex-wrap justify-between gap-2 border-b border-black/5 pb-2">
                        <dt class="opacity-70">{{ __('Issue date') }}</dt>
                        <dd class="font-medium" x-text="invoice.invoice_date || '—'"></dd>
                    </div>
                    <div class="flex flex-wrap justify-between gap-2 border-b border-black/5 pb-2">
                        <dt class="opacity-70">{{ __('Due date') }}</dt>
                        <dd class="font-medium" x-text="invoice.due_date || '—'"></dd>
                    </div>
                    <div class="flex flex-wrap justify-between gap-2 border-b border-black/5 pb-2">
                        <dt class="opacity-70">{{ __('Currency') }}</dt>
                        <dd class="font-mono font-semibold uppercase" x-text="invoice.currency"></dd>
                    </div>
                </dl>
            </div>
        </div>

        {{-- Bill To --}}
        <div class="{{ $t['clientCard'] }}" style="{{ $pc['cardStyle'] }}">
            <p class="{{ $t['sectionTitle'] }} mb-2">{{ __('Bill to') }}</p>
            <template x-if="client">
                <div class="space-y-1 text-sm">
                    <p class="font-semibold" x-text="client.name"></p>
                    <p class="opacity-80" x-show="client.company" x-text="client.company"></p>
                    <p class="whitespace-pre-line opacity-80" x-show="client.address" x-text="client.address"></p>
                    <p class="opacity-80" x-show="client.email" x-text="client.email"></p>
                    <p class="opacity-80" x-show="client.phone" x-text="client.phone"></p>
                </div>
            </template>
            <template x-if="!client">
                <p class="text-sm opacity-60">{{ __('No client selected') }}</p>
            </template>
        </div>
    </div>

    {{-- ITEMS TABLE --}}
    <div class="mt-8 overflow-x-auto">
        <table class="{{ $t['table'] }}">
            <thead>
                <tr>
                    <th class="{{ $t['th'] }}" style="{{ $pc['thStyle'] }}">{{ __('Item') }}</th>
                    <th class="{{ $t['th'] }} w-24 text-end" style="{{ $pc['thStyle'] }}">{{ __('Qty') }}</th>
                    <th class="{{ $t['th'] }} w-28 text-end" style="{{ $pc['thStyle'] }}">{{ __('Price') }}</th>
                    <th class="{{ $t['th'] }} w-28 text-end" style="{{ $pc['thStyle'] }}">{{ __('Total') }}</th>
                </tr>
            </thead>
            <tbody>
                <template x-for="(row, idx) in items" :key="row.id ?? 'r-' + idx">
                    <tr>
                        <td class="{{ $t['td'] }}">
                            <p class="font-medium" x-text="row.name"></p>
                            <p class="mt-0.5 text-xs opacity-60" x-show="row.description" x-text="row.description"></p>
                        </td>
                        <td class="{{ $t['td'] }} text-end tabular-nums" x-text="row.quantity"></td>
                        <td class="{{ $t['td'] }} text-end tabular-nums" x-text="formatMoney(row.unit_price)"></td>
                        <td class="{{ $t['td'] }} text-end font-medium tabular-nums" x-text="formatMoney(lineTotal(row))"></td>
                    </tr>
                </template>
            </tbody>
        </table>
    </div>

    {{-- TOTALS --}}
    <div class="{{ $t['totalsBox'] }}">
        <div class="{{ $t['totalsInner'] }}" style="{{ $pc['totalsStyle'] }}">
            <div class="flex justify-between gap-4">
                <span class="opacity-70" x-text="strings.subtotal"></span>
                <span class="tabular-nums font-medium" x-text="formatMoney(invoice.subtotal)"></span>
            </div>
            <div class="flex justify-between gap-4">
                <span class="opacity-70" x-text="strings.tax"></span>
                <span class="tabular-nums font-medium" x-text="formatMoney(invoice.tax_total)"></span>
            </div>
            <div class="flex justify-between gap-4">
                <span class="opacity-70" x-text="strings.discount"></span>
                <span class="tabular-nums font-medium" x-text="formatMoney(invoice.discount_total)"></span>
            </div>
            <div class="flex justify-between gap-4">
                <span class="opacity-70" x-text="strings.shipping"></span>
                <span class="tabular-nums font-medium" x-text="formatMoney(invoice.shipping_total)"></span>
            </div>
            <div class="flex justify-between gap-4 {{ $t['grand'] }}" style="{{ $pc['grandStyle'] }}">
                <span x-text="strings.grand"></span>
                <span class="tabular-nums" x-text="invoice.currency + ' ' + formatMoney(invoice.total)"></span>
            </div>
        </div>
    </div>

    {{-- NOTES --}}
    <div class="{{ $t['notes'] }}" x-show="invoice.notes">
        <p class="{{ $t['sectionTitle'] }} mb-2">{{ __('Notes') }}</p>
        <p class="whitespace-pre-line text-sm" x-text="invoice.notes"></p>
    </div>

    {{-- TERMS --}}
    <div class="mt-6" x-show="invoice.terms">
        <p class="{{ $t['sectionTitle'] }} mb-2">{{ __('Terms') }}</p>
        <p class="whitespace-pre-line text-sm" x-text="invoice.terms"></p>
    </div>

    {{-- SIGNATURE + THANK YOU --}}
    <div class="{{ $t['signature'] }}">
        <div class="max-w-md flex-1">
            <p class="{{ $t['sectionTitle'] }} mb-2">{{ __('Thank you') }}</p>
            <p class="text-sm" x-text="document.custom.thank_you"></p>
        </div>
        <div class="flex-1 sm:text-end">
            <p class="{{ $t['sectionTitle'] }} mb-2">{{ __('Signature') }}</p>
            <p class="text-sm font-medium" x-text="document.custom.signature_label"></p>
            <div class="{{ $t['sigLine'] }} mx-auto sm:ms-auto sm:me-0"></div>
        </div>
    </div>

    {{-- FOOTER --}}
    <div class="{{ $t['footer'] }}">
        <p class="mx-auto max-w-xl text-center text-[11px]" x-text="document.custom.legal_footer"></p>
    </div>
</div>
