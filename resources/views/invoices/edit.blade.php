@php
    use App\Enums\InvoiceStatus;
@endphp
<x-app-layout>
    <x-slot name="header">
        <x-clay.page-header :title="__('Invoice details')" />
    </x-slot>

    <x-clay.card class="max-w-3xl">
        <form method="post" action="{{ route('invoices.update', $invoice) }}" class="space-y-8">
            @csrf
            @method('PUT')
            <div>
                <x-input-label for="client_id" :value="__('Client')" />
                <select id="client_id" name="client_id" class="clay-select">
                    <option value="">{{ __('None') }}</option>
                    @foreach ($clients as $c)
                        <option value="{{ $c->id }}" @selected(old('client_id', $invoice->client_id) == $c->id)>{{ $c->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="grid gap-6 sm:grid-cols-2">
                <div>
                    <x-input-label for="status" :value="__('Status')" />
                    <select id="status" name="status" class="clay-select">
                        @foreach (InvoiceStatus::cases() as $st)
                            <option value="{{ $st->value }}" @selected(old('status', $invoice->status) === $st->value)>{{ $st->label() }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <x-input-label for="direction" :value="__('Direction')" />
                    <select id="direction" name="direction" class="clay-select">
                        <option value="ltr" @selected(old('direction', $invoice->direction) === 'ltr')>LTR</option>
                        <option value="rtl" @selected(old('direction', $invoice->direction) === 'rtl')>RTL</option>
                    </select>
                </div>
                <div>
                    <x-input-label for="invoice_date" :value="__('Invoice date')" />
                    <x-text-input id="invoice_date" name="invoice_date" type="date" class="mt-1 block w-full" :value="old('invoice_date', $invoice->invoice_date?->format('Y-m-d'))" required />
                </div>
                <div>
                    <x-input-label for="due_date" :value="__('Due date')" />
                    <x-text-input id="due_date" name="due_date" type="date" class="mt-1 block w-full" :value="old('due_date', $invoice->due_date?->format('Y-m-d'))" />
                </div>
                <div>
                    <x-input-label for="currency" :value="__('Currency')" />
                    <x-text-input id="currency" name="currency" maxlength="3" class="mt-1 block w-full uppercase" :value="old('currency', $invoice->currency)" required />
                </div>
                <div>
                    <x-input-label for="shipping_total" :value="__('Shipping')" />
                    <x-text-input id="shipping_total" name="shipping_total" type="number" step="0.01" min="0" class="mt-1 block w-full" :value="old('shipping_total', $invoice->shipping_total)" />
                </div>
            </div>
            <div>
                <x-input-label for="notes" :value="__('Notes')" />
                <textarea id="notes" name="notes" rows="3" class="mt-1 block w-full rounded-clay border border-clay-hairline bg-clay-canvas px-4 py-3 text-sm text-clay-ink shadow-sm focus:border-clay-ink focus:outline-none focus:ring-2 focus:ring-clay-teal/20">{{ old('notes', $invoice->notes) }}</textarea>
            </div>
            <div>
                <x-input-label for="terms" :value="__('Terms')" />
                <textarea id="terms" name="terms" rows="3" class="mt-1 block w-full rounded-clay border border-clay-hairline bg-clay-canvas px-4 py-3 text-sm text-clay-ink shadow-sm focus:border-clay-ink focus:outline-none focus:ring-2 focus:ring-clay-teal/20">{{ old('terms', $invoice->terms) }}</textarea>
            </div>
            <div class="flex justify-between gap-3">
                <x-clay.button href="{{ route('invoices.show', $invoice) }}" variant="secondary">{{ __('Cancel') }}</x-clay.button>
                <x-primary-button>{{ __('Save') }}</x-primary-button>
            </div>
        </form>
    </x-clay.card>
</x-app-layout>
