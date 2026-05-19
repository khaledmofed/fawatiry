<x-app-layout>
    <x-slot name="header">
        <x-clay.page-header :title="__('Dashboard')" :subtitle="__('Revenue, pipeline health, and recent activity.')" />
    </x-slot>

    <div class="space-y-10">
        <x-flash />

        <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
            <x-clay.stat-card accent="pink" :title="__('Total invoices')" :value="(string) $totalInvoices" />
            <x-clay.stat-card accent="teal" :title="__('Paid')" :value="(string) $paid" />
            <x-clay.stat-card accent="lavender" :title="__('Pending / draft')" :value="(string) $pending" />
            <x-clay.stat-card accent="ochre" :title="__('Revenue (paid)')" :value="number_format($revenue, 2)" :hint="__('All time')" />
        </div>

        <div class="grid gap-6 lg:grid-cols-3">
            <x-clay.card class="lg:col-span-2 !p-0" :glass="true">
                <div class="border-b border-clay-hairline/60 px-6 py-4">
                    <h2 class="text-sm font-semibold tracking-tight text-clay-ink">{{ __('Paid revenue by month') }}</h2>
                </div>
                <div class="h-64 px-4 py-4">
                    <canvas id="revenueChart"></canvas>
                </div>
            </x-clay.card>

            <x-clay.card :glass="true" class="!p-0">
                <div class="border-b border-clay-hairline/60 px-6 py-4">
                    <h2 class="text-sm font-semibold tracking-tight text-clay-ink">{{ __('Status mix') }}</h2>
                </div>
                <div class="h-52 px-4 py-4">
                    <canvas id="statusChart"></canvas>
                </div>
            </x-clay.card>
        </div>

        <div class="grid gap-6 lg:grid-cols-2">
            <x-clay.card :glass="true" class="!p-0">
                <div class="flex items-center justify-between border-b border-clay-hairline/60 px-6 py-4">
                    <h2 class="text-sm font-semibold text-clay-ink">{{ __('Latest invoices') }}</h2>
                    <a href="{{ route('invoices.index') }}" class="text-xs font-semibold text-clay-body-strong hover:text-clay-ink">{{ __('View all') }}</a>
                </div>
                <ul class="divide-y divide-clay-hairline/50">
                    @foreach ($latestInvoices as $inv)
                        <li class="flex items-center justify-between gap-4 px-6 py-3 transition hover:bg-clay-surface-soft/50">
                            <a href="{{ route('invoices.show', $inv) }}" class="font-mono text-sm font-medium text-clay-ink hover:underline">{{ $inv->invoice_number }}</a>
                            <x-clay.badge accent="neutral" class="capitalize">{{ $inv->status }}</x-clay.badge>
                        </li>
                    @endforeach
                </ul>
            </x-clay.card>

            <x-clay.card :glass="true" class="!p-0">
                <div class="flex items-center justify-between border-b border-clay-hairline/60 px-6 py-4">
                    <h2 class="text-sm font-semibold text-clay-ink">{{ __('Recent clients') }}</h2>
                    <a href="{{ route('clients.index') }}" class="text-xs font-semibold text-clay-body-strong hover:text-clay-ink">{{ __('View all') }}</a>
                </div>
                <ul class="divide-y divide-clay-hairline/50">
                    @foreach ($recentClients as $client)
                        <li class="px-6 py-3 text-sm font-medium text-clay-body-strong transition hover:bg-clay-surface-soft/50">{{ $client->name }}</li>
                    @endforeach
                </ul>
            </x-clay.card>
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                if (!window.Chart) return;
                const revCtx = document.getElementById('revenueChart');
                if (revCtx) {
                    new Chart(revCtx, {
                        type: 'line',
                        data: {
                            labels: @json($chartLabels),
                            datasets: [{
                                label: @json(__('Paid total')),
                                data: @json($chartData),
                                borderColor: '#1a3a3a',
                                backgroundColor: 'rgba(26, 58, 58, 0.12)',
                                fill: true,
                                tension: 0.35,
                            }],
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: { legend: { labels: { color: '#3a3a3a', font: { family: 'Inter' } } } },
                            scales: {
                                x: { ticks: { color: '#6a6a6a' }, grid: { color: 'rgba(229,229,229,0.6)' } },
                                y: { ticks: { color: '#6a6a6a' }, grid: { color: 'rgba(229,229,229,0.6)' } },
                            },
                        },
                    });
                }
                const stCtx = document.getElementById('statusChart');
                if (stCtx) {
                    new Chart(stCtx, {
                        type: 'doughnut',
                        data: {
                            labels: ['draft', 'pending', 'paid', 'cancelled', 'overdue'],
                            datasets: [{
                                data: [
                                    {{ $statusCounts['draft'] }},
                                    {{ $statusCounts['pending'] }},
                                    {{ $statusCounts['paid'] }},
                                    {{ $statusCounts['cancelled'] }},
                                    {{ $statusCounts['overdue'] }},
                                ],
                                backgroundColor: ['#f5f0e0', '#ffb084', '#1a3a3a', '#ebe6d6', '#ff6b5a'],
                                borderWidth: 0,
                            }],
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: { legend: { position: 'bottom', labels: { color: '#3a3a3a', boxWidth: 12, font: { family: 'Inter' } } } },
                        },
                    });
                }
            });
        </script>
    @endpush
</x-app-layout>
