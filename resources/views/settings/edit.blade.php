<x-app-layout>
    <x-slot name="header">
        <x-clay.page-header :title="__('Company settings')" />
    </x-slot>

    <x-flash />

    <x-clay.card class="max-w-3xl">
        <form method="post" action="{{ route('settings.update') }}" enctype="multipart/form-data" class="space-y-8">
            @csrf
            @method('PUT')
            <div>
                <x-input-label for="company_name" :value="__('Company name')" />
                <x-text-input id="company_name" name="company_name" class="mt-1 block w-full" :value="old('company_name', $settings->company_name)" />
                <x-input-error :messages="$errors->get('company_name')" class="mt-2" />
            </div>
            <div>
                <x-input-label for="address" :value="__('Address')" />
                <textarea id="address" name="address" rows="3" class="mt-1 block w-full rounded-clay border border-clay-hairline bg-clay-canvas px-4 py-3 text-sm text-clay-ink shadow-sm focus:border-clay-ink focus:outline-none focus:ring-2 focus:ring-clay-teal/20">{{ old('address', $settings->address) }}</textarea>
            </div>
            <div class="grid gap-6 sm:grid-cols-2">
                <div>
                    <x-input-label for="vat_number" :value="__('VAT')" />
                    <x-text-input id="vat_number" name="vat_number" class="mt-1 block w-full" :value="old('vat_number', $settings->vat_number)" />
                </div>
                <div>
                    <x-input-label for="phone" :value="__('Phone')" />
                    <x-text-input id="phone" name="phone" class="mt-1 block w-full" :value="old('phone', $settings->phone)" />
                </div>
                <div>
                    <x-input-label for="email" :value="__('Email')" />
                    <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" :value="old('email', $settings->email)" />
                </div>
                <div>
                    <x-input-label for="default_currency" :value="__('Default currency')" />
                    <x-text-input id="default_currency" name="default_currency" maxlength="3" class="mt-1 block w-full uppercase" :value="old('default_currency', $settings->default_currency)" required />
                </div>
                <div>
                    <x-input-label for="default_tax_rate" :value="__('Default tax %')" />
                    <x-text-input id="default_tax_rate" name="default_tax_rate" type="number" step="0.01" class="mt-1 block w-full" :value="old('default_tax_rate', $settings->default_tax_rate)" />
                </div>
            </div>
            <div class="grid gap-6 sm:grid-cols-3">
                <div>
                    <x-input-label for="logo" :value="__('Logo')" />
                    <input id="logo" name="logo" type="file" accept="image/*" class="mt-2 block w-full text-sm text-clay-body file:rounded-clay file:bg-clay-surface-soft file:px-3 file:py-1.5 file:text-xs file:font-semibold" />
                    @if ($settings->logo_path)
                        <img src="{{ \App\Support\PublicStorage::url($settings->logo_path) }}" class="mt-3 h-16 rounded-clay-lg border border-clay-hairline object-contain" alt="">
                    @endif
                </div>
                <div>
                    <x-input-label for="signature" :value="__('Signature')" />
                    <input id="signature" name="signature" type="file" accept="image/*" class="mt-2 block w-full text-sm text-clay-body file:rounded-clay file:bg-clay-surface-soft file:px-3 file:py-1.5 file:text-xs file:font-semibold" />
                    @if ($settings->signature_path)
                        <img src="{{ \App\Support\PublicStorage::url($settings->signature_path) }}" class="mt-3 h-16 rounded-clay-lg border border-clay-hairline object-contain" alt="">
                    @endif
                </div>
                <div>
                    <x-input-label for="stamp" :value="__('Stamp')" />
                    <input id="stamp" name="stamp" type="file" accept="image/*" class="mt-2 block w-full text-sm text-clay-body file:rounded-clay file:bg-clay-surface-soft file:px-3 file:py-1.5 file:text-xs file:font-semibold" />
                    @if ($settings->stamp_path)
                        <img src="{{ \App\Support\PublicStorage::url($settings->stamp_path) }}" class="mt-3 h-16 rounded-clay-lg border border-clay-hairline object-contain" alt="">
                    @endif
                </div>
            </div>
            <div class="flex justify-end">
                <x-primary-button>{{ __('Save settings') }}</x-primary-button>
            </div>
        </form>
    </x-clay.card>
</x-app-layout>
