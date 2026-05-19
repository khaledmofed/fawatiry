<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div class="flex flex-wrap items-center gap-2">
                <x-clay.button href="{{ route('invoices.show', $invoice) }}" variant="ghost" class="!h-9 !px-3 !text-sm">
                    ← {{ __('Back') }}
                </x-clay.button>
                <span class="rounded-full border border-clay-hairline bg-clay-surface-soft px-3 py-1 font-mono text-xs font-semibold text-clay-ink">{{ $invoice->invoice_number }}</span>
            </div>
            <div class="flex flex-wrap gap-2">
                <button type="button" @click="$dispatch('editor:save-all')"
                    class="inline-flex h-9 items-center rounded-clay bg-clay-primary px-4 text-xs font-semibold text-clay-on-primary shadow-sm hover:bg-clay-primary-active">
                    {{ __('Save') }}
                </button>
                <button type="button" @click="$dispatch('editor:export-pdf')"
                    class="inline-flex h-9 items-center rounded-clay border border-clay-hairline bg-white/80 px-4 text-xs font-semibold text-clay-ink shadow-sm hover:bg-clay-surface-soft">
                    {{ __('Export PDF') }}
                </button>
                <button type="button" @click="$dispatch('editor:print')"
                    class="inline-flex h-9 items-center rounded-clay border border-clay-hairline bg-white/80 px-4 text-xs font-semibold text-clay-ink shadow-sm hover:bg-clay-surface-soft">
                    {{ __('Print') }}
                </button>
                <button type="button" @click="$dispatch('editor:zoom-out')"
                    class="inline-flex h-9 w-9 items-center justify-center rounded-clay border border-clay-hairline bg-white/80 text-clay-ink hover:bg-clay-surface-soft" title="{{ __('Zoom out') }}">−</button>
                <button type="button" @click="$dispatch('editor:zoom-in')"
                    class="inline-flex h-9 w-9 items-center justify-center rounded-clay border border-clay-hairline bg-white/80 text-clay-ink hover:bg-clay-surface-soft" title="{{ __('Zoom in') }}">+</button>
            </div>
            <span class="text-xs font-medium text-clay-muted" id="editor-status-msg"></span>
        </div>
    </x-slot>

    <div
        id="invoice-editor-root"
        class="-mx-4 -my-8 flex sm:-mx-6 lg:-mx-8"
        style="height: calc(100vh - 10.5rem)"
        x-data="invoiceEditor"
        @editor:save-all.window="saveAll()"
        @editor:export-pdf.window="exportPdf()"
        @editor:print.window="print()"
        @editor:zoom-in.window="zoomIn()"
        @editor:zoom-out.window="zoomOut()"
    >
        {{-- ================================================================
             LEFT PANEL — Accordion settings
             ================================================================ --}}
        <aside class="flex w-[340px] shrink-0 flex-col overflow-y-auto border-e border-clay-hairline bg-white shadow-sm">

            {{-- TEMPLATE --}}
            <div class="border-b border-clay-hairline">
                <button type="button" @click="sidebar.template = !sidebar.template"
                    class="flex w-full items-center justify-between px-4 py-3 text-left text-sm font-semibold text-clay-ink hover:bg-slate-50">
                    <span>{{ __('Template') }}</span>
                    <svg class="h-4 w-4 shrink-0 transition-transform duration-200" :class="sidebar.template ? 'rotate-180' : ''"
                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>
                <div x-show="sidebar.template" class="space-y-3 px-4 pb-4">
                    <select x-model="templatePick"
                        class="block w-full rounded-clay border border-clay-hairline bg-white px-3 py-2 text-sm text-clay-ink shadow-sm">
                        <template x-for="t in templates" :key="t.id">
                            <option :value="String(t.id)" x-text="t.name"></option>
                        </template>
                    </select>
                    <button type="button" @click="applyTemplate()"
                        class="w-full rounded-clay bg-clay-primary px-3 py-2 text-xs font-semibold text-clay-on-primary hover:bg-clay-primary-active"
                        x-text="strings.applyTemplate"></button>
                </div>
            </div>

            {{-- DISPLAY SETTINGS --}}
            <div class="border-b border-clay-hairline">
                <button type="button" @click="sidebar.display = !sidebar.display"
                    class="flex w-full items-center justify-between px-4 py-3 text-left text-sm font-semibold text-clay-ink hover:bg-slate-50">
                    <span>{{ __('Display Settings') }}</span>
                    <svg class="h-4 w-4 shrink-0 transition-transform duration-200" :class="sidebar.display ? 'rotate-180' : ''"
                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>
                <div x-show="sidebar.display" class="space-y-3 px-4 pb-4">
                    <div>
                        <label class="mb-1 block text-xs font-medium text-clay-muted">{{ __('Direction') }}</label>
                        <select x-model="invoice.direction" @change="queueMetaSave()"
                            class="block w-full rounded-clay border border-clay-hairline bg-white px-3 py-2 text-sm shadow-sm">
                            <option value="ltr">LTR — {{ __('Left to Right') }}</option>
                            <option value="rtl">RTL — {{ __('Right to Left') }}</option>
                        </select>
                    </div>
                    <div>
                        <label class="mb-1 block text-xs font-medium text-clay-muted">{{ __('Status') }}</label>
                        <select x-model="invoice.status" @change="queueMetaSave()"
                            class="block w-full rounded-clay border border-clay-hairline bg-white px-3 py-2 text-sm shadow-sm">
                            @foreach (\App\Enums\InvoiceStatus::cases() as $st)
                                <option value="{{ $st->value }}">{{ $st->label() }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            {{-- LOGO --}}
            <div class="border-b border-clay-hairline">
                <button type="button" @click="sidebar.logo = !sidebar.logo"
                    class="flex w-full items-center justify-between px-4 py-3 text-left text-sm font-semibold text-clay-ink hover:bg-slate-50">
                    <span>{{ __('Logo') }}</span>
                    <svg class="h-4 w-4 shrink-0 transition-transform duration-200" :class="sidebar.logo ? 'rotate-180' : ''"
                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>
                <div x-show="sidebar.logo" class="space-y-3 px-4 pb-4">
                    <div class="flex items-center gap-3">
                        <div class="flex h-14 w-14 shrink-0 items-center justify-center overflow-hidden rounded-xl border border-clay-hairline bg-white shadow-sm">
                            <template x-if="company.logo_url">
                                <img :src="company.logo_url" alt="" class="max-h-full max-w-full object-contain" />
                            </template>
                            <template x-if="!company.logo_url">
                                <span class="text-[9px] font-semibold uppercase text-slate-400">{{ __('No logo') }}</span>
                            </template>
                        </div>
                        <div class="flex flex-col gap-1.5">
                            <button type="button" @click="openLogoPicker()"
                                class="rounded-clay border border-clay-hairline bg-white px-3 py-1.5 text-xs font-semibold text-clay-ink shadow-sm hover:bg-slate-50">
                                {{ __('Change logo') }}
                            </button>
                            <a :href="company.settings_url" class="text-xs text-teal-700 underline hover:text-teal-900">
                                {{ __('Edit company info →') }}
                            </a>
                        </div>
                    </div>
                    <div>
                        <label class="mb-1 flex items-center justify-between text-xs font-medium text-clay-muted">
                            <span>{{ __('Logo scale') }}</span>
                            <span class="font-mono" x-text="Math.round((document.logo?.scale ?? 1) * 100) + '%'"></span>
                        </label>
                        <input type="range" min="30" max="200" step="5" class="w-full accent-teal-600"
                            :value="Math.round((document.logo?.scale ?? 1) * 100)"
                            @input="document.logo = document.logo || {}; document.logo.scale = +$event.target.value / 100; queueDesignSave()" />
                    </div>
                    <p class="text-[11px] text-clay-muted">{{ __('Drag the ⇄ handle on the preview to nudge the logo position.') }}</p>
                </div>
            </div>

            {{-- INVOICE DETAILS --}}
            <div class="border-b border-clay-hairline">
                <button type="button" @click="sidebar.invoice = !sidebar.invoice"
                    class="flex w-full items-center justify-between px-4 py-3 text-left text-sm font-semibold text-clay-ink hover:bg-slate-50">
                    <span>{{ __('Invoice Details') }}</span>
                    <svg class="h-4 w-4 shrink-0 transition-transform duration-200" :class="sidebar.invoice ? 'rotate-180' : ''"
                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>
                <div x-show="sidebar.invoice" class="space-y-3 px-4 pb-4">
                    <div class="rounded-clay bg-slate-50 px-3 py-2 text-xs text-clay-muted">
                        {{ __('Number') }}: <span class="font-mono font-semibold text-clay-ink" x-text="invoice.number"></span>
                    </div>
                    <div>
                        <label class="mb-1 block text-xs font-medium text-clay-muted">{{ __('Issue date') }}</label>
                        <input type="date" x-model="invoice.invoice_date" @change="queueMetaSave()"
                            class="block w-full rounded-clay border border-clay-hairline bg-white px-3 py-2 text-sm shadow-sm" />
                    </div>
                    <div>
                        <label class="mb-1 block text-xs font-medium text-clay-muted">{{ __('Due date') }}</label>
                        <input type="date" x-model="invoice.due_date" @change="queueMetaSave()"
                            class="block w-full rounded-clay border border-clay-hairline bg-white px-3 py-2 text-sm shadow-sm" />
                    </div>
                    <div>
                        <label class="mb-1 block text-xs font-medium text-clay-muted">{{ __('Currency') }}</label>
                        <input type="text" maxlength="3" x-model="invoice.currency" @change="queueMetaSave()"
                            class="block w-full rounded-clay border border-clay-hairline bg-white px-3 py-2 text-sm font-mono uppercase shadow-sm"
                            placeholder="USD" />
                    </div>
                </div>
            </div>

            {{-- BILL TO --}}
            <div class="border-b border-clay-hairline">
                <button type="button" @click="sidebar.client = !sidebar.client"
                    class="flex w-full items-center justify-between px-4 py-3 text-left text-sm font-semibold text-clay-ink hover:bg-slate-50">
                    <span>{{ __('Bill To') }}</span>
                    <svg class="h-4 w-4 shrink-0 transition-transform duration-200" :class="sidebar.client ? 'rotate-180' : ''"
                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>
                <div x-show="sidebar.client" class="space-y-3 px-4 pb-4">
                    <div>
                        <label class="mb-1 block text-xs font-medium text-clay-muted">{{ __('Select client') }}</label>
                        <select x-model="invoice.client_id" @change="onClientAssigned()"
                            class="block w-full rounded-clay border border-clay-hairline bg-white px-3 py-2 text-sm shadow-sm">
                            <option value="">{{ __('No client') }}</option>
                            <template x-for="c in clients" :key="c.id">
                                <option :value="String(c.id)" x-text="c.name + (c.company ? ' — ' + c.company : '')"></option>
                            </template>
                        </select>
                    </div>
                    <template x-if="client">
                        <div class="space-y-3">
                            <div>
                                <label class="mb-1 block text-xs font-medium text-clay-muted">{{ __('Name') }}</label>
                                <input type="text" x-model="client.name" @change="queueClientSave()"
                                    class="block w-full rounded-clay border border-clay-hairline bg-white px-3 py-2 text-sm shadow-sm" />
                            </div>
                            <div>
                                <label class="mb-1 block text-xs font-medium text-clay-muted">{{ __('Company') }}</label>
                                <input type="text" x-model="client.company" @change="queueClientSave()"
                                    class="block w-full rounded-clay border border-clay-hairline bg-white px-3 py-2 text-sm shadow-sm" />
                            </div>
                            <div>
                                <label class="mb-1 block text-xs font-medium text-clay-muted">{{ __('Address') }}</label>
                                <textarea x-model="client.address" rows="3" @change="queueClientSave()"
                                    class="block w-full resize-y rounded-clay border border-clay-hairline bg-white px-3 py-2 text-sm shadow-sm"></textarea>
                            </div>
                            <div>
                                <label class="mb-1 block text-xs font-medium text-clay-muted">{{ __('Email') }}</label>
                                <input type="email" x-model="client.email" @change="queueClientSave()"
                                    class="block w-full rounded-clay border border-clay-hairline bg-white px-3 py-2 text-sm shadow-sm" />
                            </div>
                            <div>
                                <label class="mb-1 block text-xs font-medium text-clay-muted">{{ __('Phone') }}</label>
                                <input type="text" x-model="client.phone" @change="queueClientSave()"
                                    class="block w-full rounded-clay border border-clay-hairline bg-white px-3 py-2 text-sm shadow-sm" />
                            </div>
                        </div>
                    </template>
                    <p x-show="!client" class="text-sm text-clay-muted">{{ __('Select a client above to edit their details.') }}</p>
                </div>
            </div>

            {{-- LINE ITEMS --}}
            <div class="border-b border-clay-hairline">
                <button type="button" @click="sidebar.items = !sidebar.items"
                    class="flex w-full items-center justify-between px-4 py-3 text-left text-sm font-semibold text-clay-ink hover:bg-slate-50">
                    <div class="flex items-center gap-2">
                        <span>{{ __('Line Items') }}</span>
                        <span class="rounded-full bg-slate-100 px-2 py-0.5 text-[10px] font-bold text-slate-500" x-text="items.length"></span>
                    </div>
                    <svg class="h-4 w-4 shrink-0 transition-transform duration-200" :class="sidebar.items ? 'rotate-180' : ''"
                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>
                <div x-show="sidebar.items" class="space-y-3 px-4 pb-4">
                    <template x-for="(row, idx) in items" :key="row.id ?? 'new-' + idx">
                        <div class="rounded-xl border border-clay-hairline bg-slate-50/60 p-3">
                            <div class="mb-2 flex items-center justify-between">
                                <span class="text-[10px] font-bold uppercase tracking-wider text-clay-muted"
                                    x-text="'{{ __('Item') }} #' + (idx + 1)"></span>
                                <button type="button" @click="removeRow(idx)"
                                    class="flex h-5 w-5 items-center justify-center rounded-full text-rose-500 hover:bg-rose-100"
                                    title="{{ __('Remove') }}">×</button>
                            </div>
                            <div class="space-y-2">
                                <input type="text" x-model="row.name" @input="queueItemsSave()"
                                    class="block w-full rounded-clay border border-clay-hairline bg-white px-2.5 py-1.5 text-sm shadow-sm"
                                    placeholder="{{ __('Item name') }}" />
                                <input type="text" x-model="row.description" @input="queueItemsSave()"
                                    class="block w-full rounded-clay border border-clay-hairline bg-white px-2.5 py-1.5 text-xs text-clay-muted shadow-sm"
                                    placeholder="{{ __('Description (optional)') }}" />
                                <div class="grid grid-cols-2 gap-2">
                                    <div>
                                        <label class="mb-0.5 block text-[10px] font-medium text-clay-muted">{{ __('Qty') }}</label>
                                        <input type="number" step="0.001" x-model.number="row.quantity" @input="queueItemsSave()"
                                            class="block w-full rounded-clay border border-clay-hairline bg-white px-2.5 py-1.5 text-sm text-end shadow-sm" />
                                    </div>
                                    <div>
                                        <label class="mb-0.5 block text-[10px] font-medium text-clay-muted">{{ __('Price') }}</label>
                                        <input type="number" step="0.01" x-model.number="row.unit_price" @input="queueItemsSave()"
                                            class="block w-full rounded-clay border border-clay-hairline bg-white px-2.5 py-1.5 text-sm text-end shadow-sm" />
                                    </div>
                                </div>
                                <div class="flex justify-between text-xs">
                                    <span class="text-clay-muted">{{ __('Line total') }}</span>
                                    <span class="font-semibold tabular-nums text-clay-ink"
                                        x-text="invoice.currency + ' ' + formatMoney(lineTotal(row))"></span>
                                </div>
                            </div>
                        </div>
                    </template>

                    {{-- Add items controls --}}
                    <div class="flex flex-col gap-2">
                        <select x-model="productPick"
                            class="block w-full rounded-clay border border-clay-hairline bg-white px-3 py-2 text-sm shadow-sm">
                            <option value="">{{ __('Select product') }}</option>
                            <template x-for="p in products" :key="p.id">
                                <option :value="String(p.id)" x-text="p.name + ' — ' + formatMoney(p.price)"></option>
                            </template>
                        </select>
                        <div class="grid grid-cols-2 gap-2">
                            <button type="button" @click="addProductRow()"
                                class="rounded-clay bg-clay-primary px-3 py-2 text-xs font-semibold text-clay-on-primary hover:bg-clay-primary-active"
                                x-text="strings.addLine"></button>
                            <button type="button" @click="addBlankRow()"
                                class="rounded-clay border border-clay-hairline bg-white px-3 py-2 text-xs font-semibold text-clay-ink hover:bg-slate-50"
                                x-text="strings.emptyLine"></button>
                        </div>
                    </div>

                    {{-- Shipping + Grand Total --}}
                    <div class="rounded-xl border border-clay-hairline bg-white p-3 text-sm">
                        <div class="flex items-center justify-between gap-2 pb-2">
                            <span class="text-clay-muted" x-text="strings.shipping"></span>
                            <input type="number" step="0.01" x-model.number="invoice.shipping_total" @change="queueMetaSave()"
                                class="w-28 rounded-clay border border-clay-hairline px-2 py-1 text-end text-sm tabular-nums shadow-sm" />
                        </div>
                        <div class="flex justify-between border-t border-clay-hairline pt-2 font-semibold">
                            <span x-text="strings.grand"></span>
                            <span class="tabular-nums" x-text="invoice.currency + ' ' + formatMoney(invoice.total)"></span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- NOTES & TERMS --}}
            <div class="border-b border-clay-hairline">
                <button type="button" @click="sidebar.notes = !sidebar.notes"
                    class="flex w-full items-center justify-between px-4 py-3 text-left text-sm font-semibold text-clay-ink hover:bg-slate-50">
                    <span>{{ __('Notes & Terms') }}</span>
                    <svg class="h-4 w-4 shrink-0 transition-transform duration-200" :class="sidebar.notes ? 'rotate-180' : ''"
                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>
                <div x-show="sidebar.notes" class="space-y-3 px-4 pb-4">
                    <div>
                        <label class="mb-1 block text-xs font-medium text-clay-muted">{{ __('Notes') }}</label>
                        <textarea x-model="invoice.notes" rows="3" @input="queueMetaSave()"
                            class="block w-full resize-y rounded-clay border border-clay-hairline bg-white px-3 py-2 text-sm shadow-sm"
                            placeholder="{{ __('Payment instructions, bank details…') }}"></textarea>
                    </div>
                    <div>
                        <label class="mb-1 block text-xs font-medium text-clay-muted">{{ __('Terms & Conditions') }}</label>
                        <textarea x-model="invoice.terms" rows="2" @input="queueMetaSave()"
                            class="block w-full resize-y rounded-clay border border-clay-hairline bg-white px-3 py-2 text-sm shadow-sm"></textarea>
                    </div>
                    <div>
                        <label class="mb-1 block text-xs font-medium text-clay-muted">{{ __('Thank you message') }}</label>
                        <textarea x-model="document.custom.thank_you" rows="2" @input="queueDesignSave()"
                            class="block w-full resize-y rounded-clay border border-clay-hairline bg-white px-3 py-2 text-sm shadow-sm"></textarea>
                    </div>
                    <div>
                        <label class="mb-1 block text-xs font-medium text-clay-muted">{{ __('Signature label') }}</label>
                        <input type="text" x-model="document.custom.signature_label" @change="queueDesignSave()"
                            class="block w-full rounded-clay border border-clay-hairline bg-white px-3 py-2 text-sm shadow-sm" />
                    </div>
                    <div>
                        <label class="mb-1 block text-xs font-medium text-clay-muted">{{ __('Legal footer') }}</label>
                        <textarea x-model="document.custom.legal_footer" rows="2" @input="queueDesignSave()"
                            class="block w-full resize-y rounded-clay border border-clay-hairline bg-white px-3 py-2 text-sm shadow-sm"></textarea>
                    </div>
                </div>
            </div>

            {{-- STAMPS --}}
            <div class="border-b border-clay-hairline">
                <button type="button" @click="sidebar.stamps = !sidebar.stamps"
                    class="flex w-full items-center justify-between px-4 py-3 text-left text-sm font-semibold text-clay-ink hover:bg-slate-50">
                    <div class="flex items-center gap-2">
                        <span x-text="strings.stamp"></span>
                        <span x-show="stampsOnPage().length"
                            class="rounded-full bg-teal-100 px-2 py-0.5 text-[10px] font-bold text-teal-700"
                            x-text="stampsOnPage().length" x-cloak></span>
                    </div>
                    <svg class="h-4 w-4 shrink-0 transition-transform duration-200" :class="sidebar.stamps ? 'rotate-180' : ''"
                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>
                <div x-show="sidebar.stamps" class="space-y-3 px-4 pb-4">
                    <p class="text-[11px] leading-snug text-clay-muted" x-text="strings.stampsHint"></p>
                    <input type="file" x-ref="stampFile" class="hidden" accept="image/png,image/jpeg,image/webp,image/gif"
                        @change="uploadStampFile($event)" />
                    <button type="button" @click="$refs.stampFile.click()"
                        class="w-full rounded-clay border border-clay-hairline bg-white px-3 py-2 text-xs font-semibold text-clay-ink shadow-sm hover:bg-slate-50"
                        x-text="strings.uploadStamp"></button>

                    <template x-if="stampsOnPage().length">
                        <div class="grid grid-cols-3 gap-2">
                            <template x-for="(st, i) in stampsOnPage()" :key="st.id">
                                <button type="button"
                                    class="relative overflow-hidden rounded-xl border-2 p-1 transition"
                                    :class="selectedStampId === st.id ? 'border-teal-500 shadow-md' : 'border-clay-hairline hover:border-slate-300'"
                                    @click="selectedStampId = st.id">
                                    <img :src="stampUrlFor(st)" alt="" class="h-12 w-full object-contain" />
                                </button>
                            </template>
                        </div>
                    </template>

                    <div x-show="selectedStamp() && selectedStamp().path" x-cloak class="space-y-3">
                        <div>
                            <label class="mb-1 flex justify-between text-xs text-clay-muted">
                                <span x-text="strings.stampWidth"></span>
                                <span class="font-mono" x-text="Math.round(selectedStamp().width_pct) + '%'"></span>
                            </label>
                            <input type="range" min="8" max="45" step="1" class="w-full accent-teal-600"
                                :value="selectedStamp().width_pct"
                                @input="selectedStamp().width_pct = +$event.target.value; queueDesignSave()" />
                        </div>
                        <div>
                            <label class="mb-1 block text-xs text-clay-muted" x-text="strings.stampRotate"></label>
                            <input type="number" step="1"
                                class="block w-full rounded-clay border border-clay-hairline bg-white px-3 py-1.5 text-sm shadow-sm"
                                :value="selectedStamp().rotation"
                                @change="selectedStamp().rotation = +$event.target.value; queueDesignSave()" />
                        </div>
                        <div class="grid grid-cols-2 gap-2">
                            <button type="button" @click="resetStampPosition()"
                                class="rounded-clay border border-clay-hairline bg-white px-3 py-2 text-xs font-semibold text-clay-ink hover:bg-slate-50"
                                x-text="strings.resetStamp"></button>
                            <button type="button" @click="removeSelectedStamp()"
                                class="rounded-clay border border-rose-200 bg-rose-50 px-3 py-2 text-xs font-semibold text-rose-800 hover:bg-rose-100"
                                x-text="strings.removeStamp"></button>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Spacer to push content up --}}
            <div class="flex-1 bg-slate-50/50"></div>
        </aside>

        {{-- ================================================================
             RIGHT PANEL — A4 Preview
             ================================================================ --}}
        <div class="flex flex-1 overflow-y-auto bg-slate-200/80 p-8">
            <div class="mx-auto">
                <div id="invoice-a4-scale" class="origin-top transition-transform duration-200">
                    <div id="invoice-a4-canvas" class="relative mx-auto min-h-[297mm] w-[210mm] overflow-visible rounded-sm shadow-2xl ring-1 ring-black/5">
                        <div id="invoice-body-layer" class="relative z-0 min-h-[297mm] overflow-hidden rounded-sm">
                            @include('invoices.preview.frame-view', ['theme' => $theme, 'themeSlug' => $themeSlug])
                        </div>

                        {{-- Stamps — draggable on the A4 preview --}}
                        <template x-for="st in stampsOnPage()" :key="st.id">
                            <div
                                class="absolute z-[200] cursor-grab select-none touch-none rounded-sm"
                                :class="stampDragging && selectedStampId === st.id
                                    ? 'ring-2 ring-teal-500 ring-offset-2 shadow-2xl'
                                    : selectedStampId === st.id
                                        ? 'ring-2 ring-teal-400/80 ring-offset-1 shadow-lg'
                                        : 'ring-1 ring-black/20 shadow-lg'"
                                :style="stampBoxStyle(st)"
                                @pointerdown="stampPointerDown($event, st)"
                            >
                                <img :src="stampUrlFor(st)" alt=""
                                    class="pointer-events-none block h-auto w-full rounded-sm opacity-[0.97]"
                                    draggable="false" />
                                <button
                                    type="button"
                                    class="absolute -right-1 -top-1 flex h-6 w-6 items-center justify-center rounded-full bg-white text-xs font-bold text-rose-600 shadow ring-1 ring-rose-200 hover:bg-rose-50"
                                    title="{{ __('Remove stamp') }}"
                                    @click.stop="removeStampById(st.id)"
                                >×</button>
                            </div>
                        </template>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            window.__INVOICE_EDITOR__ = @json($editorPayload);
        </script>
    @endpush
</x-app-layout>
