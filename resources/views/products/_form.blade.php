@php $product = $product ?? null; @endphp
<div class="grid gap-6 sm:grid-cols-2">
    <div class="sm:col-span-2">
        <x-input-label for="name" :value="__('Name')" />
        <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $product->name ?? '')" required />
        <x-input-error class="mt-2" :messages="$errors->get('name')" />
    </div>
    <div class="sm:col-span-2">
        <x-input-label for="description" :value="__('Description')" />
        <textarea id="description" name="description" rows="3" class="mt-1 block w-full rounded-clay border-clay-hairline bg-clay-canvas px-4 py-3 text-sm text-clay-ink shadow-sm focus:border-clay-ink focus:outline-none focus:ring-2 focus:ring-clay-teal/20">{{ old('description', $product->description ?? '') }}</textarea>
        <x-input-error class="mt-2" :messages="$errors->get('description')" />
    </div>
    <div>
        <x-input-label for="price" :value="__('Price')" />
        <x-text-input id="price" name="price" type="number" step="0.01" min="0" class="mt-1 block w-full" :value="old('price', $product->price ?? '0')" required />
        <x-input-error class="mt-2" :messages="$errors->get('price')" />
    </div>
    <div>
        <x-input-label for="tax_rate" :value="__('Tax rate %')" />
        <x-text-input id="tax_rate" name="tax_rate" type="number" step="0.01" min="0" max="100" class="mt-1 block w-full" :value="old('tax_rate', $product->tax_rate ?? '0')" />
        <x-input-error class="mt-2" :messages="$errors->get('tax_rate')" />
    </div>
    <div>
        <x-input-label for="sku" :value="__('SKU')" />
        <x-text-input id="sku" name="sku" type="text" class="mt-1 block w-full" :value="old('sku', $product->sku ?? '')" />
        <x-input-error class="mt-2" :messages="$errors->get('sku')" />
    </div>
    <div class="flex items-center gap-2 pt-8">
        <input type="hidden" name="is_active" value="0">
        <input id="is_active" name="is_active" type="checkbox" value="1" class="rounded border-zinc-600 bg-zinc-900 text-indigo-500" @checked(old('is_active', $product->is_active ?? true))>
        <x-input-label for="is_active" :value="__('Active')" class="!mb-0" />
    </div>
</div>
