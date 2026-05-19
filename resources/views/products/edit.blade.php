<x-app-layout>
    <x-slot name="header">
        <x-clay.page-header :title="__('Edit product')" />
    </x-slot>

    <x-clay.card>
        <form method="post" action="{{ route('products.update', $product) }}" class="space-y-8">
            @csrf
            @method('PUT')
            @include('products._form', ['product' => $product])
            <div class="flex justify-end gap-3">
                <x-clay.button href="{{ route('products.index') }}" variant="secondary">{{ __('Cancel') }}</x-clay.button>
                <x-primary-button>{{ __('Update') }}</x-primary-button>
            </div>
        </form>
    </x-clay.card>
</x-app-layout>
