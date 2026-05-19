<x-app-layout>
    <x-slot name="header">
        <x-clay.page-header :title="__('Invoice')" :subtitle="$invoice->invoice_number">
            <x-slot name="actions">
                <x-clay.button href="{{ route('invoices.editor', $invoice) }}" variant="primary">{{ __('Open editor') }}</x-clay.button>
                <x-clay.button href="{{ route('invoices.edit', $invoice) }}" variant="secondary">{{ __('Edit details') }}</x-clay.button>
                <x-clay.button href="{{ route('invoices.pdf.dompdf', $invoice) }}" variant="ghost">{{ __('PDF (server)') }}</x-clay.button>
            </x-slot>
        </x-clay.page-header>
    </x-slot>

    <div class="space-y-8">
        <x-clay.card>
            <div class="grid gap-8 sm:grid-cols-2">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wider text-clay-muted">{{ __('Client') }}</p>
                    <p class="mt-2 text-lg font-semibold text-clay-ink">{{ $invoice->client?->name ?? '—' }}</p>
                    @if ($invoice->client?->company)
                        <p class="text-sm text-clay-muted">{{ $invoice->client->company }}</p>
                    @endif
                </div>
                <div class="space-y-2 text-sm text-clay-body">
                    <p><span class="font-semibold text-clay-body-strong">{{ __('Status') }}:</span> <span class="capitalize">{{ $invoice->status }}</span></p>
                    <p><span class="font-semibold text-clay-body-strong">{{ __('Invoice date') }}:</span> {{ $invoice->invoice_date?->format('Y-m-d') }}</p>
                    <p><span class="font-semibold text-clay-body-strong">{{ __('Due') }}:</span> {{ $invoice->due_date?->format('Y-m-d') ?? '—' }}</p>
                    <p><span class="font-semibold text-clay-body-strong">{{ __('Direction') }}:</span> {{ strtoupper($invoice->direction) }}</p>
                </div>
            </div>
        </x-clay.card>

        <x-clay.table-shell>
            <table class="min-w-full divide-y divide-clay-hairline/80 text-sm">
                <thead class="bg-clay-surface-soft/80 text-left text-xs font-semibold uppercase tracking-wider text-clay-muted">
                    <tr>
                        <th class="px-6 py-3">{{ __('Item') }}</th>
                        <th class="px-6 py-3 text-end">{{ __('Qty') }}</th>
                        <th class="px-6 py-3 text-end">{{ __('Price') }}</th>
                        <th class="px-6 py-3 text-end">{{ __('Total') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-clay-hairline/60 bg-white/50">
                    @forelse ($invoice->items as $item)
                        <tr>
                            <td class="px-6 py-3 font-medium text-clay-ink">{{ $item->name }}</td>
                            <td class="px-6 py-3 text-end text-clay-body">{{ $item->quantity }}</td>
                            <td class="px-6 py-3 text-end text-clay-body">{{ number_format((float) $item->unit_price, 2) }}</td>
                            <td class="px-6 py-3 text-end font-semibold text-clay-ink">{{ number_format((float) $item->line_total, 2) }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="px-6 py-10 text-center text-clay-muted">{{ __('No line items.') }}</td></tr>
                    @endforelse
                </tbody>
            </table>
            <div class="flex flex-wrap justify-end gap-6 border-t border-clay-hairline/60 bg-clay-surface-soft/40 px-6 py-4 text-sm">
                <span class="text-clay-muted">{{ __('Subtotal') }} <span class="ms-2 font-semibold text-clay-ink">{{ number_format((float) $invoice->subtotal, 2) }}</span></span>
                <span class="text-clay-muted">{{ __('Tax') }} <span class="ms-2 font-semibold text-clay-ink">{{ number_format((float) $invoice->tax_total, 2) }}</span></span>
                <span class="text-lg font-semibold text-clay-ink">{{ __('Total') }} {{ $invoice->currency }} {{ number_format((float) $invoice->total, 2) }}</span>
            </div>
        </x-clay.table-shell>

        @if ($invoice->notes)
            <x-clay.card>
                <p class="text-xs font-semibold uppercase tracking-wider text-clay-muted">{{ __('Notes') }}</p>
                <p class="mt-2 text-sm leading-relaxed text-clay-body">{{ $invoice->notes }}</p>
            </x-clay.card>
        @endif
    </div>
</x-app-layout>
