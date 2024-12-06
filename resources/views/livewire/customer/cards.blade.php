<?php

use function Livewire\Volt\{state, WithPolling};
use App\Models\Customer;
//

$action = state('action', 'all');

$all_c = state('all_c', Customer::count());
$online_c = state('online_c', Customer::where('is_online', '=', '1')->count());
$offline_c = state('offline_c', Customer::where('is_online', '=', '0')->count());

$customers = state('customers', Customer::orderBy('created_at', 'DESC')->get());

$updateActionState = function ($action) {
    $this->action = $action;

    $this->all_c = Customer::count();
    $this->online_c = Customer::where('is_online', '=', '1')->count();
    $this->offline_c = Customer::where('is_online', '=', '0')->count();

    // Update the customers variable based on the action
    if ($action === "all") {
        $this->customers =  Customer::orderBy('created_at', 'DESC')->get();
    } else if ($action === "online") {
        $this->customers = Customer::where('is_online', '=', '1')->orderBy('created_at', 'DESC')->get();
    } else if ($action === "offline") {
        $this->customers = Customer::where('is_online', '=', '0')->orderBy('created_at', 'DESC')->get();
    } else {
        $this->customers = Customer::orderBy('created_at', 'DESC')->get();
    }
};


?>

<div wire:poll.2000ms="updateActionState('{{$action}}')">
    <div class="grid grid-cols-5 gap-5 mt-5 lg:grid-cols-4">
        <!-- status -->
        <div class="card col-span-1" wire:click="updateActionState('all')" style="cursor: pointer;">
            <div class="card-body">
                <h5 class="uppercase text-xs tracking-wider font-extrabold">Total Customers</h5>
                <h1 class="capitalize text-lg mt-1 mb-1"><span class="num-3">{{$this->all_c}} </span> </h1>

            </div>
        </div>
        <!-- status -->

        <!-- status -->
        <div class="card col-span-1" wire:click="updateActionState('online')" style="cursor: pointer;">
            <div class="card-body">
                <h5 class="uppercase text-xs tracking-wider font-extrabold">Online Customers</h5>
                <h1 class="capitalize text-lg mt-1 mb-1"><span class="num-3">{{$this->online_c}} </span> <span
                        class="text-xs tracking-widest font-extrabold"><span
                            class="text-blue-900 font-extrabold">online</span> / <span
                            class="num-2">{{$this->all_c}}</span> Active Customers</span></h1>
            </div>
        </div>
        <!-- status -->

        <!-- status -->
        <div class="card col-span-1" wire:click="updateActionState('offline')" style="cursor: pointer;">
            <div class="card-body">
                <h5 class="uppercase text-xs tracking-wider font-extrabold">Un-active Customers</h5>
                <h1 class="capitalize text-lg mt-1 mb-1"><span class="num-3">{{$this->offline_c}} </span> </h1>
            </div>
        </div>
        <!-- status -->

        <!-- status -->
        <div class="card col-span-1 lg:col-span-2" style="cursor: pointer;" x-data=""
            x-on:click.prevent="$dispatch('open-modal', 'add-customer-modal')">
            <div class="card-body flex items-center">
                <div class="px-3 py-2 rounded bg-primary-600 text-black mr-3">
                    <i class="fad fa-plus text-lg"></i>
                </div>
                <h5 class="uppercase text-xs tracking-wider font-extrabold"><span style="color: green;">Add New Customer</span></h5>
            </div>
        </div>
        <!-- status -->

    </div>


    @if (count($customers) > 0)
    {{-- @livewire('customers.list-all', ['customers' => $customers, 'action' => $action]) --}}

    {{-- @foreach ($customers as $device) --}}
    @livewire('customer.list-all', ['customers' => $customers, 'action' => $action], key($customers[0]->id))
    {{-- @endforeach --}}
    @else
    <div class="card col-span-2 xl:col-span-1 mt-4">
        <div class="card-header">{{$action}} customers</div>
        <div class="card-body" align='center'>No Data</div>
    </div>
    @endif


    {{-- Create Customer Modal --}}
    <x-modal name="add-customer-modal" :show="$errors->isNotEmpty()" focusable class="p-6 sm:max-w-sm">
        <form wire:submit.prevent="registerCustomer" class="p-6">

            <h2 class="text-lg font-medium text-gray-900">
                {{ __('Register New Customer') }}
            </h2>

            <div class="mt-6 w-100 p-4 flex flex-row justify-between">
                <div class="w-1/2 mr-4">
                    <label for="name" class="block text-sm font-medium text-gray-700">{{ __('Customer Name') }}</label>
                    <input wire:model="name" id="name" name="name" type="text"
                        class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                        placeholder="{{ __('Customer Name') }}" />
                    <x-input-error :messages="$errors->get('name')" class="mt-2" />
                </div>
                <div class="w-1/2 ml-4">
                    <label for="email" class="block text-sm font-medium text-gray-700">{{ __('Email') }}</label>
                    <input wire:model="email" id="email" name="email" type="email"
                        class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                        placeholder="{{ __('Email') }}" />
                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                </div>
            </div>

            <div class="mt-6 w-100 p-4 flex flex-row justify-between">
                <div class="w-1/2 mr-4">
                    <label for="phone" class="block text-sm font-medium text-gray-700">{{ __('Phone') }}</label>
                    <input wire:model="phone" id="phone" name="phone" type="text"
                        class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                        placeholder="{{ __('Phone') }}" />
                    <x-input-error :messages="$errors->get('phone')" class="mt-2" />
                </div>
                <div class="w-1/2 ml-4">
                    <label for="address" class="block text-sm font-medium text-gray-700">{{ __('Address') }}</label>
                    <textarea wire:model="address" id="address" name="address" rows="3"
                        class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                        placeholder="{{ __('Address') }}"></textarea>
                    <x-input-error :messages="$errors->get('address')" class="mt-2" />
                </div>
            </div>

            <div class="mt-6 flex justify-end p-4">
                <x-secondary-button x-on:click="$dispatch('close')">
                    {{ __('Cancel') }}
                </x-secondary-button>
                &nbsp;&nbsp;&nbsp;&nbsp;
                <x-danger-button class="ms-3">
                    {{ __('Register Customer') }}
                </x-danger-button>
            </div>
        </form>
    </x-modal>
    {{-- end modal --}}


</div>
