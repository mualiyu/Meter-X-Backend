<?php

use function Livewire\Volt\{state};
use App\Models\Customer; // Add this line to import the Customer model
use SimpleSoftwareIO\QrCode\Facades\QrCode;

// // Fetch the latest customers
// state('customers', Customer::all());
//

state('customers', []);
state('action', []);

state('selectedCustomer', '');
state('showDetail', false);

$customer_name = state('customer_name', '');
$address = state('address', '');
$email = state('email', '');
$phone = state('phone', '');
$email_verified_at = state('email_verified_at', '');
$password = state('password', '');
$otp = state('otp', '');
$is_online = state('is_online', '');
$customer_id = state('customer_id', '');

// delete confirmation
$delete_confirmation = state('delete_confirmation', '');


$showCustomerDetail =  function ($customerId) {
    if ($customerId) {
        // Fetch the customer details based on the selected customer ID
        $this->selectedCustomer = Customer::find($customerId);

        $this->customer_name = $this->selectedCustomer->name;
        $this->address = $this->selectedCustomer->address;
        $this->email = $this->selectedCustomer->email;
        $this->phone = $this->selectedCustomer->phone;
        // $this->email_verified_at = $this->selectedCustomer->email_verified_at;
        // $this->password = $this->selectedCustomer->password;
        // $this->otp = $this->selectedCustomer->otp;
        $this->is_online = $this->selectedCustomer->is_online;

        $this->customer_id = $this->selectedCustomer->id;

        // return $this->selectedCustomer
        Log::alert($this->selectedCustomer);

        $this->showDetail = true;
        // You can add any additional logic here, such as redirecting or displaying a modal
    } else {
        $this->selectedCustomer = null; // Reset if no customer is selected
    }
};

$updateCustomer = function () {

    $customer = Customer::where('id', '=', $this->customer_id)->update([
        "name"=>$this->customer_name,
        "address"=>$this->address,
        "email"=>$this->email,
        "phone"=>$this->phone,
        // "email_verified_at"=>$this->email_verified_at,
        // "password"=>$this->password,
        // "otp"=>$this->otp,
        // "is_online"=>$this->is_online,
    ]);

    $this->reset([
        'customer_name',
        'address',
        'email',
        'phone',
        'customer_id',
        // 'email_verified_at',
        // 'password',
        // 'otp',
        // 'is_online',
    ]);

    if ($this->action === "online") {
        $this->customers = Customer::where('is_online', '=', '1')->orderBy('created_at', 'DESC')->get();
    } else {
        $this->customers = Customer::orderBy('created_at', 'DESC')->get();
    }

    session()->flash('message', "Customer is updated successfully!");
};

$hideDetail = function () {
    $this->showDetail = false;
    $this->reset([
        'customer_name',
        'address',
        'email',
        'phone',
        'customer_id',
        // 'email_verified_at',
        // 'password',
        // 'otp',
        // 'is_online',
    ]);
};

$deleteCustomer = function ($customerId) {
    // if ($this->delete_confirmation !== 'delete') {
    //     session()->flash('d_message', "Delete confirmation does not match.");
    //     return;
    // }
    $this->validate([
        'delete_confirmation' => 'required|string|max:255|in:delete',
    ]);

    $del = Customer::where('id', '=', $customerId)->delete();

    if ($del) {
        if ($this->action === "online") {
            $this->customers = Customer::where('is_online', '=', '1')->orderBy('created_at', 'DESC')->get();
        } else {
            $this->customers = Customer::orderBy('created_at', 'DESC')->get();
        }

        $this->reset([
            'delete_confirmation',
        ]);

        session()->flash('d_message', "You have deleted one customer");
    }
};
?>

<div>
    @if (session()->has('message'))
    <br>
    <div class="alert alert-success mb-5">
        {{ session('message') }}
    </div>
    @endif

    @if ($showDetail)
    <div class="w-full max-w-full p-4 mt-4 bg-white border border-gray-200 rounded-lg shadow sm:p-6 md:p-8 dark:bg-gray-800 dark:border-gray-700"
        wire:display="showCustomerDetail">
        <form class="space-y-6" wire:submit.prevent="updateCustomer">
            <h5 class="text-xl font-medium text-gray-900 dark:text-white">Customer Details</h5>
            <div class="flex flex-row justify-between">
                <input type="hidden" wire:model="customer_idd">
                <div class="w-1/2 pr-4">
                    <label for="customer_name" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Customer
                        Name</label>
                    <input type="text" name="customer_name" id="customer_name" wire:model="customer_name"
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white"
                        placeholder="Customer Name" required />
                </div>
                <div class="w-1/2 ml-4">
                    <label for="address" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white"
                        >Address</label>
                    <input type="text" name="address" id="address" wire:model="address"
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white"
                        placeholder="Address" required />
                </div>
            </div>
            <div class="flex flex-row justify-between mt-4">
                <div class="w-1/3 pr-4">
                    <label for="email"
                        class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Email</label>
                    <input type="email" name="email" id="email" wire:model="email"
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white"
                        placeholder="Email" required />
                </div>
                <div class="w-1/3 ml-4 pr-4">
                    <label for="phone"
                        class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Phone</label>
                    <input type="text" name="phone" id="phone" wire:model="phone"
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white"
                        placeholder="Phone" required />
                </div>

                <!-- <div class="w-1/3">
                    <label for="is_online" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Is Online</label>
                    <select name="is_online" id="is_online" wire:model="is_online"
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white"
                        required>
                        <option value="">Select Online Status</option>
                        <option value="1">Online</option>
                        <option value="0">Offline</option>
                    </select>
                </div> -->
                <div class="w-1/3">
                    <label for="is_online" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Is Online</label>
                    <span wire:model="is_online"  class="badge {{ $is_online ? 'bg-green-500' : 'bg-red-500' }} text-white text-sm">
                        {{ $is_online ? 'Online' : 'Offline' }}
                    </span>
                </div>

            </div>
            <div class="flex flex-row justify-between mt-4">
                {{-- <div class="w-1/3">
                    <label for="email_verified_at" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Email Verified At</label>
                    <input type="text" name="email_verified_at" id="email_verified_at" wire:model="email_verified_at"
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white"
                        placeholder="Email Verified At" required />
                </div> --}}
                {{-- <div class="w-1/3">
                    <label for="password"
                        class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Password</label>
                    <input type="password" name="password" id="password" wire:model="password"
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white"
                        placeholder="Password" required />
                </div> --}}
                {{-- <div class="w-1/3 ml-4">
                    <label for="otp"
                        class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">OTP</label>
                    <input type="text" name="otp" id="otp" wire:model="otp"
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white"
                        placeholder="OTP" required />
                </div> --}}

            </div>

            <div class="flex justify-between">
                <button type="submit"
                    class="w-1/2 text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">Update
                    Customer</button> &nbsp;&nbsp;&nbsp;
                <button wire:click="hideDetail()" type="button"
                    class="w-1/2 ml-4 text-white bg-red-700 hover:bg-red-800 focus:ring-4 focus:outline-none focus:ring-red-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-red-600 dark:hover:bg-red-700 dark:focus:ring-red-800">Close</button>
            </div>
        </form>
    </div>
    @endif


    {{-- List customers --}}
    <div class="card col-span-2 xl:col-span-1 mt-4">
        <div class="card-header">{{$this->action}} Customers</div>

        <table class="table-auto w-full text-left">
            <thead>
                <tr>
                    <th class="px-4 py-2 border-r">#</th>
                    <th class="px-4 py-2 border-r">Customer Name</th>
                    {{-- <th class="px-4 py-2 border-r">Address</th>  --}}
                    <th class="px-4 py-2 border-r">Email</th>
                    <th class="px-4 py-2 border-r">Phone</th>
                    {{-- <th class="px-4 py-2 border-r">Email Verified At</th> --}}
                    {{-- <th class="px-4 py-2 border-r">Password</th> --}}
                    {{-- <th class="px-4 py-2 border-r">OTP</th> --}}
                    <th class="px-4 py-2">Is Online</th>
                    <th class="px-4 py-2">Action</th>
                </tr>
            </thead>
            <tbody class="text-gray-600">
                @if (count($this->customers)>0)
                @foreach($this->customers as $customer) {{-- Loop through customers --}}
                <?php
                // $qrcode = QrCode::size(100)->generate("MX_839e97f4f0fa79e5e6510ba9550b6ce6");
                ?>
                <tr>
                    <td
                        class="border border-l-0 px-4 py-2 text-center {{ $customer->is_online ? 'text-green-500' : 'text-red-500' }}">
                        {{-- <i class="fad fa-circle"></i> --}}
                        {{-- {!! $qrcode !!} --}}
                    </td>
                    <td class="border border-l-0 px-4 py-2">{{ $customer->name }}</td> {{-- Customer name --}}
                    {{-- <td class="border border-l-0 px-4 py-2">{{ $customer->address }}</td> --}}
                    <td class="border border-l-0 px-4 py-2">{{ $customer->email }}</td> {{-- Customer email --}}
                    <td class="border border-l-0 px-4 py-2">{{ $customer->phone }}</td> {{-- Customer phone --}}
                    {{-- <td class="border border-l-0 px-4 py-2">{{ $customer->email_verified_at }}</td> --}}
                    {{-- <td class="border border-l-0 px-4 py-2">{{ $customer->password }}</td>  --}}
                    {{-- <td class="border border-l-0 px-4 py-2">{{ $customer->otp }}</td>  --}}
                    <td class="border border-l-0 px-4 py-2">
                        <span class="badge {{ $customer->is_online ? 'bg-green-500' : 'bg-red-500' }} text-white text-sm">
                            {{ $customer->is_online ? 'Online' : 'Offline' }}
                        </span>
                    </td> {{-- Customer online status --}}
                    <td class="border border-l-0 border-r-0 px-4 py-2">
                        <div class="flex justify-between">
                            <button wire:click="showCustomerDetail({{ $customer->id }})"
                                class="w-1/2 mt-1 border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                View Details
                            </button>

                            <button
                            {{-- wire:click="deleteCustomer({{ $customer->id }})" --}}
                                x-data=""
                                x-on:click.prevent="$dispatch('open-modal', 'delete-customer-modal-{{$customer->id}}')"
                                class="text-red-500 hover:text-red-700 ml-4">
                                <i class="fas fa-trash"></i>
                            </button>
                            <x-modal name="delete-customer-modal-{{$customer->id}}" :show="$errors->isNotEmpty()" focusable maxWidth="sm" class="p-6">
                                <form wire:submit.prevent="deleteCustomer({{ $customer->id }})" class="p-6">
                                <div class="mb-4">
                                    <p class="text-sm font-medium text-gray-900">
                                        Are you sure you want to delete customer: "{{ $customer->name }}"?
                                    </p>
                                </div>
                                <div class="mt-6 p-4">
                                    <label for="delete_confirmation" class="block text-sm font-medium text-gray-700">{{ __('Type "delete" to confirm') }}</label>
                                    <input wire:model="delete_confirmation" id="delete_confirmation" name="delete_confirmation" type="text"
                                        class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                        placeholder="{{ __('Type "delete" to confirm') }}" />
                                    <x-input-error :messages="$errors->get('delete_confirmation')" class="mt-2" />
                                </div>
                                <div class="flex justify-between mt-4">
                                    <button
                                    type="submit"
                                    {{-- wire:click="deleteCustomer({{ $customer->id }})"  --}}
                                    class="w-1/2 bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded">
                                        Delete
                                    </button>
                                    <button
                                    x-on:click="$dispatch('close')"
                                    class="w-1/2 ml-4 bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                                        Cancel
                                    </button>
                                </div>
                                </form>
                            </x-modal>
                        </div>
                    </td>
                </tr>
                @endforeach {{-- End of customers loop --}}
                @else
                <tr align="center">
                    <td class="border border-l-0 border-r-0 px-4 py-2" colspan="10">No Customer Found. <a href="#">Create
                            One!</a> </td>
                </tr>
                @endif
            </tbody>
        </table>
    </div>


</div>
