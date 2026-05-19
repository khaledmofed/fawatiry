<x-app-layout>
    <x-slot name="header">
        <x-clay.page-header :title="__('Invoices')">
            <x-slot name="actions">
                <x-clay.button href="{{ route('invoice-templates.index') }}" variant="primary">{{ __('New invoice') }}</x-clay.button>
            </x-slot>
        </x-clay.page-header>
    </x-slot>

    <div class="space-y-6">
        <x-flash />
        <x-clay.table-shell>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-clay-hairline/80 text-sm">
                    <thead class="bg-clay-surface-soft/80 text-left text-xs font-semibold uppercase tracking-wider text-clay-muted">
                        <tr>
                            <th class="px-6 py-4">{{ __('Number') }}</th>
                            <th class="px-6 py-4">{{ __('Client') }}</th>
                            <th class="px-6 py-4">{{ __('Status') }}</th>
                            <th class="px-6 py-4">{{ __('Date') }}</th>
                            <th class="px-6 py-4 text-end">{{ __('Total') }}</th>
                            <th class="px-6 py-4 text-end">{{ __('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-clay-hairline/60 bg-white/50 text-clay-body">
                        @forelse ($invoices as $inv)
                            <tr class="transition hover:bg-clay-surface-soft/60">
                                <td class="px-6 py-4 font-mono font-semibold text-clay-ink">{{ $inv->invoice_number }}</td>
                                <td class="px-6 py-4">{{ $inv->client?->name ?? '—' }}</td>
                                <td class="px-6 py-4">
                                    @php
                                        $accent = match ($inv->status) {
                                            'draft' => 'neutral',
                                            'pending' => 'warning',
                                            'paid' => 'success',
                                            'overdue' => 'danger',
                                            'cancelled' => 'neutral',
                                            default => 'neutral',
                                        };
                                    @endphp
                                    <x-clay.badge :accent="$accent" class="capitalize">{{ $inv->status }}</x-clay.badge>
                                </td>
                                <td class="px-6 py-4">{{ $inv->invoice_date?->format('Y-m-d') }}</td>
                                <td class="px-6 py-4 text-end font-semibold text-clay-ink">{{ $inv->currency }} {{ number_format((float) $inv->total, 2) }}</td>
                                <td class="px-6 py-4 text-end space-x-3 whitespace-nowrap">
                                    <a href="{{ route('invoices.editor', $inv) }}" class="text-sm font-semibold text-clay-teal hover:underline">{{ __('Editor') }}</a>
                                    <a href="{{ route('invoices.show', $inv) }}" class="text-sm font-semibold text-clay-muted hover:text-clay-ink">{{ __('View') }}</a>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="px-6 py-14 text-center text-clay-muted">{{ __('No invoices yet.') }}</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="border-t border-clay-hairline/60 bg-clay-surface-soft/40 px-6 py-4">{{ $invoices->links() }}</div>
        </x-clay.table-shell>
    </div>
</x-app-layout>
