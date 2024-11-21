<?php

use function Livewire\Volt\{state};
use App\Models\Device; // Add this line to import the Customer model

// Fetch the latest customers
$action = state('action', 'available');

$available_d = state('available_d', Device::where('status', '=', 'in_stock')->orderBy('created_at', 'DESC')->get());
$sold_d = state('sold_d', Device::where('status', '=', 'purchased')->orderBy('created_at', 'DESC')->get());
$online_d = state('online_d', Device::where('is_online', '=', '1')->orderBy('created_at', 'DESC')->get());
// public function setAction($value)
// {
//     $this->action = $value;
// }

$devices = state('devices', Device::where('status', '=', 'in_stock')->orderBy('created_at', 'DESC')->get());

$updateActionState = function ($action) {
    $this->action = $action;
    // Update the devices variable based on the action
    if ($action === "available") {
        $this->devices = Device::where('status', '=', 'in_stock')->orderBy('created_at', 'DESC')->get();
    } else if ($action === "sold") {
        $this->devices = Device::where('status', '=', 'purchased')->orderBy('created_at', 'DESC')->get();
    } else if ($action === "online") {
        $this->devices = Device::where('is_online', '=', '1')->orderBy('created_at', 'DESC')->get();
    } else {
        $this->devices = Device::orderBy('created_at', 'DESC')->get();
    }
    // Log::alert($this->devices);
};

$device_name = state('device_name', '');
$meter_id = state('meter_id', 'MX_' . bin2hex(random_bytes(16)));
// $is_online = state('is_online', false);
$status = state('status', '');
$type = state('type', '');
$price = state('price', '');
$stock_status = state('stock_status', '');

$createDevice = function () {
    // Validate the input data
    $this->validate([
        'device_name' => 'required|string|max:255',
        'meter_id' => 'required|string|max:255',
        // 'is_online' => 'required|boolean',
        'status' => 'required|string',
        'type' => 'required|string',
        'price' => 'required|numeric|min:0',
        'stock_status' => 'required|string',
    ]);

    // Create a new device record
    Device::create([
        'device_name' => $this->device_name,
        'meter_id' => $this->meter_id,
        // 'is_online' => $this->is_online,
        'status' => $this->status,
        'type' => $this->type,
        'price' => $this->price,
        'stock_status' => $this->stock_status,
    ]);

    // Optionally reset the form fields
    $this->reset([
        'device_name',
        'meter_id',
        // 'is_online',
        'status',
        'type',
        'price',
        'stock_status',
    ]);

    // Optionally dispatch an event or show a success message
    session()->flash('message', 'Device created successfully.');

    // Close the modal
    $this->dispatch('close', ['modal' => 'add-device-modal']);
    // $dispatch('close');
};


?>

<div>
    {{-- @if (session()->has('message'))
    <div class="alert alert-success mb-5">
        {{ session('message') }}
    </div>
    @endif --}}

    <div class="grid grid-cols-4 gap-6 xl:grid-cols-2">
        <!-- card -->
        <div class="card mt-6 cursor-pointer" style="cursor: pointer;" wire:click="updateActionState('available')">
            <div class="card-body flex items-center">

                <div class="px-3 py-2 rounded bg-green-600 text-white mr-3">
                    <i class="fad fa-shopping-cart"></i>
                </div>

                <div class="flex flex-col">
                    <h1 class="font-semibold"><span class="num-xx">{{count($available_d)}}</span> Available Devices</h1>
                    {{-- <p class="text-xs"><span class="num-2"></span> items</p> --}}
                </div>

            </div>
        </div>
        <!-- end card -->

        <!-- card -->
        <div class="card mt-6 cursor-pointer" style="cursor: pointer;" wire:click="updateActionState('sold')">
            <div class="card-body flex items-center">

                <div class="px-3 py-2 rounded bg-indigo-600 text-white mr-3">
                    <i class="fad fa-wallet"></i>
                </div>

                <div class="flex flex-col">
                    <h1 class="font-semibold"><span class="num-xx">{{count($sold_d)}}</span> Sold Devices</h1>
                    {{-- <p class="text-xs"><span class="num-2"></span> payments</p> --}}
                </div>

            </div>
        </div>
        <!-- end card -->

        <!-- card -->
        <div class="card mt-6 xl:mt-1 cursor-pointer" style="cursor: pointer;" wire:click="updateActionState('online')">
            <div class="card-body flex items-center">

                <div class="px-3 py-2 rounded bg-yellow-600 text-white mr-3">
                    <i class="fad fa-blog"></i>
                </div>

                <div class="flex flex-col">
                    <h1 class="font-semibold"><span class="num-xx">{{count($online_d)}}</span> Online Devices </h1>
                    {{-- <p class="text-xs"><span class="num-2"></span> active</p> --}}
                </div>

            </div>
        </div>
        <!-- end card -->
{{-- modal create device --}}
<x-modal name="add-device-modal" :show="$errors->isNotEmpty()" focusable class="p-6">
    <form wire:submit.prevent="createDevice" class="p-6">

        <h2 class="text-lg font-medium text-gray-900">
            {{ __('Add New Device') }}
        </h2>

        <div class="mt-6 w-100 p-4 flex flex-row justify-between">
            <div class="w-1/2 mr-4">
                <label for="device_name" class="block text-sm font-medium text-gray-700">{{ __('Device Name')
                    }}</label>
                <input wire:model="device_name" id="device_name" name="device_name" type="text"
                    class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                    placeholder="{{ __('Device Name') }}" />
                <x-input-error :messages="$errors->get('device_name')" class="mt-2" />
            </div>
            <div class="w-1/2 ml-4">
                <label for="meter_id" class="block text-sm font-medium text-gray-700">{{ __('Meter ID') }}</label>
                <input wire:model="meter_id" id="meter_id" disabled @disabled(true) name="meter_id" type="text"
                    class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                    placeholder="{{ __('Meter ID') }}" />
                    <input type="hidden" wire:click="meter_id">
                <x-input-error :messages="$errors->get('meter_id')" class="mt-2" />
            </div>
        </div>

        {{-- <div class="mt-6 p-4">
            <label for="is_online" class="block text-sm font-medium text-gray-700">{{ __('Is Online') }}</label>
            <select wire:model="is_online" id="is_online"
                class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                <option value="0">{{ __('No') }}</option>
                <option value="1">{{ __('Yes') }}</option>
            </select>
            <x-input-error :messages="$errors->get('is_online')" class="mt-2" />
        </div> --}}

        <div class="mt-6 p-4 flex flex-row justify-between">
            <div class="w-1/2 mr-4">
                <label for="status" class="block text-sm font-medium text-gray-700">{{ __('Status') }}</label>
                <select wire:model="status" id="status"
                    class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    <option>{{ __('Select Status') }}</option>
                    <option value="in_stock" selected>{{ __('In Stock') }}</option>
                    <option value="purchased">{{ __('Purchased') }}</option>
                    <option value="damaged">{{ __('Damaged') }}</option>
                </select>
                <x-input-error :messages="$errors->get('status')" class="mt-2" />
            </div>
            <div class="w-1/2 ml-4">
                <label for="type" class="block text-sm font-medium text-gray-700">{{ __('Type') }}</label>
                <select wire:model="type" id="type"
                    class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    <option>{{ __('Select Type') }}</option>
                    <option value="household" selected>{{ __('Household') }}</option>
                    <option value="industrial">{{ __('Industrial') }}</option>
                </select>
                <x-input-error :messages="$errors->get('type')" class="mt-2" />
            </div>
        </div>

        <div class="mt-6 p-4 flex flex-row justify-between">
            <div class="w-1/2 mr-4">
                <label for="price" class="block text-sm font-medium text-gray-700">{{ __('Price') }}</label>
                <input wire:model="price" id="price" name="price" type="number" step="0.01"
                    class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                    placeholder="{{ __('Price') }}" />
                <x-input-error :messages="$errors->get('price')" class="mt-2" />
            </div>
            <div class="w-1/2 ml-4">
                <label for="stock_status" class="block text-sm font-medium text-gray-700">{{ __('Stock Status')
                    }}</label>
                <select wire:model="stock_status" id="stock_status"
                    class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    <option>{{ __('Select Stock') }}</option>
                    <option value="available" selected>{{ __('Available') }}</option>
                    <option value="sold">{{ __('Sold') }}</option>
                </select>
                <x-input-error :messages="$errors->get('stock_status')" class="mt-2" />
            </div>
        </div>


        <div class="mt-6 flex justify-end p-4">
            <x-secondary-button x-on:click="$dispatch('close')">
                {{ __('Cancel') }}
            </x-secondary-button>
            &nbsp;&nbsp;&nbsp;&nbsp;
            <x-danger-button class="ms-3">
                {{ __('Add Device') }}
            </x-danger-button>
        </div>
    </form>
</x-modal>
{{-- end modal --}}
        <!-- card -->
        <div class="card mt-6 xl:mt-1 cursor-pointer" style="cursor: pointer;" x-data=""
            x-on:click.prevent="$dispatch('open-modal', 'add-device-modal')">
            <div class="card-body flex items-center">

                <div class="px-3 py-2 rounded bg-primary-600 text-black mr-3">
                    <i class="fad fa-plus text-lg"></i>
                </div>
                {{-- <i class="fad fa-atom-alt"></i> --}}

                <div class="flex flex-col">
                    <h1 class="font-semibold">Add New Device</h1>
                    <p class="text-xs">Click to add a new device to the inventory.</p>
                </div>

            </div>
        </div>
        <!-- end card -->

    </div>



    {{-- <livewire:devices.list-all> --}}

        @if (count($devices) > 0)
            {{-- @livewire('devices.list-all', ['devices' => $devices, 'action' => $action]) --}}

            {{-- @foreach ($devices as $device) --}}
                @livewire('devices.list-all', ['devices' => $devices, 'action' => $action], key($devices[0]->id))
            {{-- @endforeach --}}
        @else
        <div class="card col-span-2 xl:col-span-1 mt-4">
            <div class="card-header">{{$action}} Devices</div>
            <div class="card-body" align='center'>No Data</div>
        </div>
        @endif
        {{-- , key($devices[0]->id) --}}
</div>

