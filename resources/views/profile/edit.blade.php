<x-app-layout>
    <x-slot name="header">
        <x-clay.page-header :title="__('Profile')" :subtitle="__('Manage your account and security.')" />
    </x-slot>

    <div class="mx-auto max-w-3xl space-y-6">
        <x-clay.card>
            <div class="max-w-xl">
                @include('profile.partials.update-profile-information-form')
            </div>
        </x-clay.card>

        <x-clay.card>
            <div class="max-w-xl">
                @include('profile.partials.update-password-form')
            </div>
        </x-clay.card>

        <x-clay.card>
            <div class="max-w-xl">
                @include('profile.partials.delete-user-form')
            </div>
        </x-clay.card>
    </div>
</x-app-layout>
