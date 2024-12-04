<?php

use App\Models\System;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\Rule;

use function Livewire\Volt\state;

state([
    'system_id' => fn () => System::first()->id,
    'system_name' => fn () => System::first()->system_name ?? 'Meter-X',
    'description' => fn () => System::first()->description ?? '',
]);

$updateSystemInformation = function () {
    $system = System::find($this->system_id);

    $validated = $this->validate([
        'system_name' => ['required', 'string', 'max:255'],
        'description' => ['required', 'string', 'max:255'],
    ]);

    $system->fill($validated);
    $system->save();

    $this->dispatch('system-updated', name: $system->system_name);
};

?>

<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900">
            {{ __('System Information') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600">
            {{ __("Update your system's profile information.") }}
        </p>
    </header>

    <form wire:submit="updateSystemInformation" class="mt-6 space-y-6">
        <div>
            <x-input-label for="system_name" :value="__('System Name')" />
            <x-text-input wire:model="system_name" id="system_name" name="system_name" type="text" class="mt-1 block w-full" required autofocus autocomplete="system_name" />
            <x-input-error class="mt-2" :messages="$errors->get('system_name')" />
        </div>

        <div>
            <x-input-label for="description" :value="__('Description')" />
            <textarea wire:model="description" id="description" name="description" class="mt-1 block w-full" required autocomplete="description"></textarea>
            <x-input-error class="mt-2" :messages="$errors->get('description')" />
        </div>

        <div class="flex items-center gap-4">
            <x-primary-button>{{ __('Save') }}</x-primary-button>

            <x-action-message class="me-3" on="system-updated">
                {{ __('Saved.') }}
            </x-action-message>
        </div>
    </form>
</section>
