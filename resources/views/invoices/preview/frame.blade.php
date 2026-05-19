@php
    /** @var array<string, string> $theme */
    $t = $theme;
    $heroDark = in_array($themeSlug ?? '', ['corporate-blue', 'creative-agency', 'luxury-black-gold', 'dark-modern', 'premium-gold'], true);
@endphp

<div
    class="{{ $t['wrap'] }} px-6 py-8 sm:px-10 sm:py-10"
    :dir="invoice.direction"
>
    <div class="{{ $t['topBar'] }}">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
            <div class="flex min-w-0 flex-1 items-start gap-4">
                <div
                    class="relative flex h-[4.5rem] w-[4.5rem] shrink-0 cursor-pointer items-center justify-center overflow-hidden rounded-xl border border-black/10 bg-white/90 shadow-sm ring-1 ring-black/5 transition hover:ring-teal-500/40"
                    title="{{ __('Click image to replace · drag handle to nudge') }}"
                >
                    <template x-if="company.logo_url">
                        <img
                            @click.prevent="openLogoPicker()"
                            :src="company.logo_url"
                            alt=""
                            class="max-h-full max-w-full cursor-pointer object-contain"
                            :style="logoImageStyle()"
                            draggable="false"
                        />
                    </template>
                    <template x-if="!company.logo_url">
                        <button
                            type="button"
                            class="px-2 text-center text-[10px] font-semibold uppercase leading-tight text-slate-500"
                            @click="openLogoPicker()"
                            x-text="strings.addLogo"
                        ></button>
                    </template>
                    <div
                        class="absolute bottom-0.5 end-0.5 cursor-grab rounded bg-black/55 px-1 text-[9px] font-bold text-white shadow"
                        title="{{ __('Nudge logo') }}"
                        @pointerdown.stop="logoPointerDown($event)"
                    >⇄</div>
                    <input type="file" x-ref="logoFile" class="hidden" accept="image/png,image/jpeg,image/webp,image/gif" @change="uploadLogoFile($event)" />
                </div>
                <div class="min-w-0">
                    <p class="mb-1 text-[11px] font-semibold uppercase tracking-wider {{ $heroDark ? 'text-white/70' : $t['sectionTitle'] }}">{{ __('Invoice') }}</p>
                    <h1 class="{{ $t['title'] }}" x-text="strings.invoice"></h1>
                    <p class="mt-1 text-[10px] opacity-70 {{ $heroDark ? 'text-white/60' : 'text-slate-500' }}" x-text="strings.logoHint"></p>
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

    <div class="mt-8 grid gap-6 lg:grid-cols-2">
        <div class="space-y-4">
            <div>
                <p class="{{ $t['sectionTitle'] }} mb-2">{{ __('From') }}</p>
                <div class="min-w-0 space-y-1 text-sm">
                    <p class="text-base font-bold leading-snug" x-text="company.name"></p>
                    <p class="whitespace-pre-line opacity-90" x-text="company.address || '—'"></p>
                    <p class="opacity-90" x-show="company.phone"><span x-text="company.phone"></span></p>
                    <p class="opacity-90" x-show="company.email"><span x-text="company.email"></span></p>
                    <p class="text-xs opacity-75" x-show="company.vat_number">{{ __('VAT') }}: <span x-text="company.vat_number"></span></p>
                    <a :href="company.settings_url" class="mt-2 inline-block text-xs font-semibold text-teal-700 underline decoration-teal-700/30 hover:text-teal-900" x-text="strings.companyHint"></a>
                </div>
            </div>
            <div>
                <p class="{{ $t['sectionTitle'] }} mb-2">{{ __('Invoice details') }}</p>
                <dl class="grid grid-cols-1 gap-2 text-sm">
                    <div class="flex flex-wrap justify-between gap-2 border-b border-black/5 pb-2">
                        <dt class="opacity-70">{{ __('Issue date') }}</dt>
                        <dd><input type="date" x-model="invoice.invoice_date" @change="queueMetaSave()" class="{{ $t['input'] }} text-end sm:text-start" /></dd>
                    </div>
                    <div class="flex flex-wrap justify-between gap-2 border-b border-black/5 pb-2">
                        <dt class="opacity-70">{{ __('Due date') }}</dt>
                        <dd><input type="date" x-model="invoice.due_date" @change="queueMetaSave()" class="{{ $t['input'] }} text-end sm:text-start" /></dd>
                    </div>
                    <div class="flex flex-wrap justify-between gap-2 border-b border-black/5 pb-2">
                        <dt class="opacity-70">{{ __('Currency') }}</dt>
                        <dd><input type="text" maxlength="3" x-model="invoice.currency" @change="queueMetaSave()" class="{{ $t['input'] }} w-16 font-mono uppercase text-end sm:text-start" /></dd>
                    </div>
                </dl>
            </div>
        </div>
        <div class="{{ $t['clientCard'] }}">
            <p class="{{ $t['sectionTitle'] }} mb-2">{{ __('Bill to') }}</p>
            <template x-if="client">
                <div class="space-y-2 text-sm">
                    <input type="text" x-model="client.name" @change="queueClientSave()" class="{{ $t['input'] }} font-semibold" placeholder="{{ __('Client name') }}" />
                    <input type="text" x-model="client.company" @change="queueClientSave()" class="{{ $t['input'] }}" placeholder="{{ __('Company') }}" />
                    <textarea x-model="client.address" rows="2" @change="queueClientSave()" class="{{ $t['input'] }} min-h-[2.5rem] resize-y" placeholder="{{ __('Address') }}"></textarea>
                    <input type="email" x-model="client.email" @change="queueClientSave()" class="{{ $t['input'] }}" placeholder="{{ __('Email') }}" />
                    <input type="text" x-model="client.phone" @change="queueClientSave()" class="{{ $t['input'] }}" placeholder="{{ __('Phone') }}" />
                </div>
            </template>
            <template x-if="!client">
                <p class="text-sm opacity-70" x-text="strings.noClient"></p>
            </template>
        </div>
    </div>

    <div class="mt-8 overflow-x-auto">
        <table class="{{ $t['table'] }}">
            <thead>
                <tr>
                    <th class="{{ $t['th'] }}">{{ __('Item') }}</th>
                    <th class="{{ $t['th'] }} w-24 text-end">{{ __('Qty') }}</th>
                    <th class="{{ $t['th'] }} w-28 text-end">{{ __('Price') }}</th>
                    <th class="{{ $t['th'] }} w-28 text-end">{{ __('Total') }}</th>
                    <th class="{{ $t['th'] }} w-10"></th>
                </tr>
            </thead>
            <tbody>
                <template x-for="(row, idx) in items" :key="row.id ?? 'new-' + idx">
                    <tr>
                        <td class="{{ $t['td'] }}">
                            <input type="text" x-model="row.name" @input="queueItemsSave()" class="{{ $t['input'] }}" />
                            <input type="text" x-model="row.description" @input="queueItemsSave()" class="{{ $t['input'] }} text-xs opacity-60 mt-0.5" placeholder="{{ __('Description (optional)') }}" />
                        </td>
                        <td class="{{ $t['td'] }} text-end">
                            <input type="number" step="0.001" x-model.number="row.quantity" @input="queueItemsSave()" class="{{ $t['input'] }} text-end" />
                        </td>
                        <td class="{{ $t['td'] }} text-end">
                            <input type="number" step="0.01" x-model.number="row.unit_price" @input="queueItemsSave()" class="{{ $t['input'] }} text-end" />
                        </td>
                        <td class="{{ $t['td'] }} text-end font-medium tabular-nums" x-text="formatMoney(lineTotal(row))"></td>
                        <td class="{{ $t['td'] }} text-center">
                            <button type="button" class="rounded p-1 text-rose-600 hover:bg-rose-50" @click="removeRow(idx)" title="{{ __('Remove') }}">×</button>
                        </td>
                    </tr>
                </template>
            </tbody>
        </table>
        <div class="mt-3 flex flex-wrap items-center gap-2">
            <select x-model="productPick" class="min-w-[12rem] max-w-full rounded-lg border border-black/10 bg-white px-2 py-1.5 text-sm text-gray-900 shadow-sm">
                <option value="">{{ __('Select product') }}</option>
                <template x-for="p in products" :key="p.id">
                    <option :value="String(p.id)" x-text="p.name + ' — ' + formatMoney(p.price)"></option>
                </template>
            </select>
            <button type="button" class="rounded-lg bg-slate-900 px-3 py-1.5 text-xs font-semibold text-white hover:bg-slate-800" @click="addProductRow()" x-text="strings.addLine"></button>
            <button type="button" class="rounded-lg border border-black/10 bg-white px-3 py-1.5 text-xs font-semibold hover:bg-black/5" @click="addBlankRow()" x-text="strings.emptyLine"></button>
        </div>
    </div>

    <div class="{{ $t['totalsBox'] }}">
        <div class="{{ $t['totalsInner'] }}">
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
                <input type="number" step="0.01" x-model.number="invoice.shipping_total" @change="queueMetaSave()" class="w-24 rounded border border-black/10 bg-white px-2 py-0.5 text-end text-sm tabular-nums" />
            </div>
            <div class="flex justify-between gap-4 {{ $t['grand'] }}">
                <span x-text="strings.grand"></span>
                <span class="tabular-nums" x-text="invoice.currency + ' ' + formatMoney(invoice.total)"></span>
            </div>
        </div>
    </div>

    <div class="{{ $t['notes'] }}">
        <p class="{{ $t['sectionTitle'] }} mb-2">{{ __('Notes') }}</p>
        <textarea x-model="invoice.notes" rows="3" @input="queueMetaSave()" class="w-full resize-y border-0 bg-transparent text-sm focus:ring-0" placeholder="{{ __('Payment instructions, bank details…') }}"></textarea>
    </div>

    <div class="mt-6">
        <p class="{{ $t['sectionTitle'] }} mb-2">{{ __('Terms') }}</p>
        <textarea x-model="invoice.terms" rows="2" @input="queueMetaSave()" class="w-full resize-y rounded-lg border border-black/10 bg-white/50 px-3 py-2 text-sm focus:ring-2 focus:ring-slate-300/50" placeholder="{{ __('Terms & conditions') }}"></textarea>
    </div>

    <div class="{{ $t['signature'] }}">
        <div class="max-w-md flex-1">
            <p class="{{ $t['sectionTitle'] }} mb-2">{{ __('Thank you') }}</p>
            <textarea x-model="document.custom.thank_you" rows="3" @input="queueDesignSave()" class="w-full resize-y rounded-lg border border-black/10 bg-white/40 px-3 py-2 text-sm focus:ring-2 focus:ring-teal-500/20"></textarea>
        </div>
        <div class="flex-1 sm:text-end">
            <p class="{{ $t['sectionTitle'] }} mb-2">{{ __('Signature') }}</p>
            <input type="text" x-model="document.custom.signature_label" @change="queueDesignSave()" class="{{ $t['input'] }} sm:text-end" />
            <div class="{{ $t['sigLine'] }} mx-auto sm:ms-auto sm:me-0">{{ __('Sign here') }}</div>
        </div>
    </div>

    <div class="{{ $t['footer'] }}">
        <textarea x-model="document.custom.legal_footer" rows="2" @input="queueDesignSave()" class="mx-auto w-full max-w-xl resize-none border-0 bg-transparent text-center text-[11px] focus:ring-0"></textarea>
    </div>
</div>
