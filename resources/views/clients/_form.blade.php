@php
    $client = $client ?? null;
@endphp

<div class="grid gap-6 sm:grid-cols-2">
    <div>
        <x-input-label for="name" :value="__('Name')" />
        <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $client->name ?? '')" required autofocus />
        <x-input-error class="mt-2" :messages="$errors->get('name')" />
    </div>
    <div>
        <x-input-label for="company" :value="__('Company')" />
        <x-text-input id="company" name="company" type="text" class="mt-1 block w-full" :value="old('company', $client->company ?? '')" />
        <x-input-error class="mt-2" :messages="$errors->get('company')" />
    </div>
    <div>
        <x-input-label for="email" :value="__('Email')" />
        <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" :value="old('email', $client->email ?? '')" />
        <x-input-error class="mt-2" :messages="$errors->get('email')" />
    </div>
    <div>
        <x-input-label for="phone" :value="__('Phone')" />
        <x-text-input id="phone" name="phone" type="text" class="mt-1 block w-full" :value="old('phone', $client->phone ?? '')" />
        <x-input-error class="mt-2" :messages="$errors->get('phone')" />
    </div>
    <div class="sm:col-span-2">
        <x-input-label for="address" :value="__('Address')" />
        <textarea id="address" name="address" rows="3" class="mt-1 block w-full rounded-clay border-clay-hairline bg-clay-canvas px-4 py-3 text-sm text-clay-ink shadow-sm transition placeholder:text-clay-muted-soft focus:border-clay-ink focus:outline-none focus:ring-2 focus:ring-clay-teal/20">{{ old('address', $client->address ?? '') }}</textarea>
        <x-input-error class="mt-2" :messages="$errors->get('address')" />
    </div>
    <div>
        <x-input-label for="vat_number" :value="__('VAT number')" />
        <x-text-input id="vat_number" name="vat_number" type="text" class="mt-1 block w-full" :value="old('vat_number', $client->vat_number ?? '')" />
        <x-input-error class="mt-2" :messages="$errors->get('vat_number')" />
    </div>
    <div class="sm:col-span-2">
        <x-input-label for="notes" :value="__('Notes')" />
        <textarea id="notes" name="notes" rows="3" class="mt-1 block w-full rounded-clay border-clay-hairline bg-clay-canvas px-4 py-3 text-sm text-clay-ink shadow-sm focus:border-clay-ink focus:outline-none focus:ring-2 focus:ring-clay-teal/20">{{ old('notes', $client->notes ?? '') }}</textarea>
        <x-input-error class="mt-2" :messages="$errors->get('notes')" />
    </div>
</div>
