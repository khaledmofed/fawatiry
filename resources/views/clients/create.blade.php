<x-app-layout>
    <x-slot name="header">
        <x-clay.page-header :title="__('New client')" />
    </x-slot>

    <x-clay.card>
        <form method="post" action="{{ route('clients.store') }}" class="space-y-8">
            @csrf
            @include('clients._form')
            <div class="flex justify-end gap-3">
                <x-clay.button href="{{ route('clients.index') }}" variant="secondary">{{ __('Cancel') }}</x-clay.button>
                <x-primary-button>{{ __('Save') }}</x-primary-button>
            </div>
        </form>
    </x-clay.card>
</x-app-layout>
