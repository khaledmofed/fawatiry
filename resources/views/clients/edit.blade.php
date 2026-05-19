<x-app-layout>
    <x-slot name="header">
        <x-clay.page-header :title="__('Edit client')" />
    </x-slot>

    <x-clay.card>
        <form method="post" action="{{ route('clients.update', $client) }}" class="space-y-8">
            @csrf
            @method('PUT')
            @include('clients._form', ['client' => $client])
            <div class="flex justify-end gap-3">
                <x-clay.button href="{{ route('clients.index') }}" variant="secondary">{{ __('Cancel') }}</x-clay.button>
                <x-primary-button>{{ __('Update') }}</x-primary-button>
            </div>
        </form>
    </x-clay.card>
</x-app-layout>
