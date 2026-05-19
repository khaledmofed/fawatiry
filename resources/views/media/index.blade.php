<x-app-layout>
    <x-slot name="header">
        <x-clay.page-header :title="__('Media library')" />
    </x-slot>

    <div class="space-y-8">
        <x-flash />

        <x-clay.card>
            <form action="{{ route('media.store') }}" method="post" enctype="multipart/form-data" class="flex flex-col gap-4 sm:flex-row sm:items-end">
                @csrf
                <div class="min-w-0 flex-1">
                    <x-input-label for="file" :value="__('Upload image')" />
                    <input id="file" name="file" type="file" accept="image/*" class="mt-2 block w-full text-sm text-clay-body file:me-4 file:rounded-clay file:border-0 file:bg-clay-primary file:px-4 file:py-2 file:text-sm file:font-semibold file:text-clay-on-primary hover:file:bg-clay-primary-active" required />
                    <x-input-error class="mt-2" :messages="$errors->get('file')" />
                </div>
                <x-primary-button type="submit">{{ __('Upload') }}</x-primary-button>
            </form>
        </x-clay.card>

        <div class="grid grid-cols-2 gap-4 sm:grid-cols-3 md:grid-cols-4">
            @forelse ($media as $m)
                <x-clay.card class="group !p-0 overflow-hidden" :glass="true">
                    <div class="relative">
                        <img src="{{ $m->url() }}" alt="" class="h-40 w-full object-cover">
                        <form action="{{ route('media.destroy', $m) }}" method="post" class="absolute end-2 top-2 opacity-0 transition group-hover:opacity-100" onsubmit="return confirm('{{ __('Delete?') }}');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="rounded-clay bg-clay-primary/90 px-2 py-1 text-xs font-semibold text-white shadow-sm hover:bg-clay-primary">{{ __('Delete') }}</button>
                        </form>
                        <button type="button" class="absolute bottom-2 end-2 rounded-clay bg-white/90 px-2 py-1 text-xs font-semibold text-clay-ink opacity-0 shadow-sm transition group-hover:opacity-100" onclick="navigator.clipboard.writeText('{{ $m->url() }}')">{{ __('Copy URL') }}</button>
                    </div>
                    <div class="truncate px-3 py-2 text-xs text-clay-muted">{{ $m->original_name }}</div>
                </x-clay.card>
            @empty
                <p class="col-span-full py-12 text-center text-clay-muted">{{ __('No uploads yet.') }}</p>
            @endforelse
        </div>
        <div>{{ $media->links() }}</div>
    </div>
</x-app-layout>
