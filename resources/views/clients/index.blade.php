<x-app-layout>
    <x-slot name="header">
        <x-clay.page-header :title="__('Clients')">
            <x-slot name="actions">
                <x-clay.button href="{{ route('clients.create') }}" variant="primary">{{ __('Add client') }}</x-clay.button>
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
                            <th class="px-6 py-4">{{ __('Name') }}</th>
                            <th class="px-6 py-4">{{ __('Company') }}</th>
                            <th class="px-6 py-4">{{ __('Email') }}</th>
                            <th class="px-6 py-4 text-end">{{ __('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-clay-hairline/60 bg-white/50 text-clay-body">
                        @forelse ($clients as $client)
                            <tr class="transition hover:bg-clay-surface-soft/60">
                                <td class="px-6 py-4 font-semibold text-clay-ink">{{ $client->name }}</td>
                                <td class="px-6 py-4">{{ $client->company ?? '—' }}</td>
                                <td class="px-6 py-4">{{ $client->email ?? '—' }}</td>
                                <td class="px-6 py-4 text-end space-x-3">
                                    <a href="{{ route('clients.edit', $client) }}" class="text-sm font-semibold text-clay-teal hover:underline">{{ __('Edit') }}</a>
                                    <form action="{{ route('clients.destroy', $client) }}" method="post" class="inline" onsubmit="return confirm('{{ __('Delete this client?') }}');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-sm font-semibold text-clay-error hover:underline">{{ __('Delete') }}</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-14 text-center text-clay-muted">{{ __('No clients yet.') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="border-t border-clay-hairline/60 bg-clay-surface-soft/40 px-6 py-4">{{ $clients->links() }}</div>
        </x-clay.table-shell>
    </div>
</x-app-layout>
