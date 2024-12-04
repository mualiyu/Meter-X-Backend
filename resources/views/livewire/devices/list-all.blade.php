<?php

use function Livewire\Volt\{state};
use App\Models\Device; // Add this line to import the Customer model
use SimpleSoftwareIO\QrCode\Facades\QrCode;

// // Fetch the latest customers
// state('devices', Device::all());
//

state('devices', []);
state('action', []);

state('selectedDevice', '');
state('showDetail', false);

// $device_name = state('device_name', '');
// $meter_id = state('meter_id', '');
// $status = state('status', '');
// $type = state('type', '');
// $price = state('price', '');
// $stock_status = state('stock_status', '');

state('name', '');
state('device_id', '');
state('status', '');
state('type', '');
state('price', '');
state('device_idd', '');
state('production_date', '');

// delete confirmation
$delete_confirmation = state('delete_confirmation', '');


$showDeviceDetail =  function ($deviceId) {
    if ($deviceId) {
        // Fetch the device details based on the selected device ID
        $this->selectedDevice = Device::find($deviceId);

        $this->name = $this->selectedDevice->name;
        $this->device_id = $this->selectedDevice->device_id;
        $this->status = $this->selectedDevice->status;
        $this->type = $this->selectedDevice->type;
        $this->price = $this->selectedDevice->price;
        $this->device_idd = $this->selectedDevice->id;
        $this->production_date = $this->selectedDevice->production_date;

        // return $this->selectedDevic
        Log::alert($this->selectedDevice);

        $this->showDetail = true;
        // You can add any additional logic here, such as redirecting or displaying a modal
    } else {
        $this->selectedDevice = null; // Reset if no device is selected
    }
};

$updateDevice = function () {

    $device = Device::where('id', '=', $this->device_idd)->update([
        "name"=>$this->name,
        "status"=>$this->status,
        "type"=>$this->type,
        "price"=>$this->price,
        "production_date"=>$this->production_date,
    ]);

    // $this->reset([
    //     'name',
    //     'status',
    //     'type',
    //     'price',
    // ]);

    if ($this->action === "available") {
        $this->devices = Device::where('status', '=', 'in_stock')->orderBy('created_at', 'DESC')->get();
    } else if ($this->action === "sold") {
        $this->devices = Device::where('status', '=', 'purchased')->orderBy('created_at', 'DESC')->get();
    } else if ($this->action === "online") {
        $this->devices = Device::where('is_online', '=', '1')->orderBy('created_at', 'DESC')->get();
    } else {
        $this->devices = Device::orderBy('created_at', 'DESC')->get();
    }

    session()->flash('message', "Device is updated successfully!");
};

$hideDetail = function () {
    $this->showDetail = false;
};

$deleteDevice = function ($deviceId) {
    // if ($this->delete_confirmation !== 'delete') {
    //     session()->flash('d_message', "Delete confirmation does not match.");
    //     return;
    // }
    $this->validate([
        'delete_confirmation' => 'required|string|max:255|in:delete',
    ]);

    $del = Device::where('id', '=', $deviceId)->delete();

    if ($del) {
        if ($this->action === "available") {
            $this->devices = Device::where('status', '=', 'in_stock')->orderBy('created_at', 'DESC')->get();
        } else if ($this->action === "sold") {
            $this->devices = Device::where('status', '=', 'purchased')->orderBy('created_at', 'DESC')->get();
        } else if ($this->action === "online") {
            $this->devices = Device::where('is_online', '=', '1')->orderBy('created_at', 'DESC')->get();
        } else {
            $this->devices = Device::orderBy('created_at', 'DESC')->get();
        }

        $this->reset([
            'delete_confirmation',
        ]);

        session()->flash('d_message', "You have deleted one device");
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
        wire:display="showDeviceDetail">
        <form class="space-y-6" wire:submit.prevent="updateDevice">
            <h5 class="text-xl font-medium text-gray-900 dark:text-white">Device Details</h5>
            <div class="flex flex-row justify-between">
                <input type="hidden" wire:model="device_idd">
                <div class="w-1/2">
                    <label for="name" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Device
                        Name</label>
                    <input type="text" name="name" id="name" wire:model="name"
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white"
                        placeholder="Device Name" required />
                </div>
                <div class="w-1/2 ml-4">
                    <label for="device_id" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white"
                        @disabled(true)>Device ID</label>
                    <input type="text" name="device_id" id="device_id" wire:model="device_id" disabled @disabled(true)
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white"
                        placeholder="Device ID" required />
                </div>
            </div>
            <div class="flex flex-row justify-between mt-4">
                <div class="w-1/3">
                    <label for="price"
                        class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Price</label>
                    <input type="number" name="price" id="price" wire:model="price"
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white"
                        placeholder="Price" required />
                </div>
                <div class="w-1/3 ml-4">
                    <label for="status"
                        class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Status</label>
                    <select name="status" id="status" wire:model="status"
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white"
                        required>
                        <option value="">Select Status</option>
                        <option value="in_stock">In Stock</option>
                        <option value="purchased">Purchased</option>
                        <option value="damaged">Damaged</option>
                    </select>
                </div>
                <div class="w-1/3">
                    <label for="type" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Type</label>
                    <select name="type" id="type" wire:model="type"
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white"
                        required>
                        <option value="">Select Type</option>
                        <option value="household">Household</option>
                        <option value="industrial">Industrial</option>
                    </select>
                </div>
            </div>
            <div class="flex flex-row justify-between mt-4">
                <div class="w-1/3">
                    <label for="production_date"
                        class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Production Date</label>
                    <input type="date" name="production_date" id="production_date" wire:model="production_date"
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white"
                        placeholder="Production Date" required />
                </div>
            </div>


            <div class="flex justify-between">
                <button type="submit"
                    class="w-1/2 text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">Update
                    Device</button> &nbsp;&nbsp;&nbsp;
                <button wire:click="hideDetail()" type="button"
                    class="w-1/2 ml-4 text-white bg-red-700 hover:bg-red-800 focus:ring-4 focus:outline-none focus:ring-red-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-red-600 dark:hover:bg-red-700 dark:focus:ring-red-800">Close</button>
            </div>
        </form>
    </div>
    @endif


    {{-- List devices --}}
    <div class="card col-span-2 xl:col-span-1 mt-4">
        <div class="card-header">{{$this->action}} Devices</div>

        <table class="table-auto w-full text-left">
            <thead>
                <tr>
                    <th class="px-4 py-2 border-r">#</th>
                    <th class="px-4 py-2 border-r">Device Name</th>
                    <th class="px-4 py-2 border-r">Device ID</th> {{-- New column for Device ID --}}
                    <th class="px-4 py-2 border-r">Price</th>
                    <th class="px-4 py-2 border-r">Date Added</th>
                    <th class="px-4 py-2">Action</th>
                </tr>
            </thead>
            <tbody class="text-gray-600">
                @if (count($this->devices)>0)
                @foreach($this->devices as $device) {{-- Loop through devices --}}
                <?php
                // $qrcode = QrCode::size(100)->generate("MX_839e97f4f0fa79e5e6510ba9550b6ce6");
                ?>
                <tr>
                    <td
                        class="border border-l-0 px-4 py-2 text-center {{ $device->is_online ? 'text-green-500' : 'text-red-500' }}">
                        {{-- <i class="fad fa-circle"></i> --}}
                        {{-- {!! $qrcode !!} --}}
                    </td>
                    <td class="border border-l-0 px-4 py-2">{{ $device->name }}</td> {{-- Device name --}}
                    <td class="border border-l-0 px-4 py-2">{{ $device->device_id }}</td> {{-- Device ID --}}
                    <td class="border border-l-0 px-4 py-2">{{ $device->price }}</td> {{-- Device price --}}
                    <td class="border border-l-0 px-4 py-2">
                        @php
                        $diff = $device->created_at->diffForHumans();
                        @endphp
                        {{ $diff }}
                    </td> {{-- Device creation date --}}
                    <td class="border border-l-0 border-r-0 px-4 py-2">
                        <div class="flex justify-between">
                            <button wire:click="showDeviceDetail({{ $device->id }})"
                                class="w-1/2 mt-1 border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                View Details
                            </button>

                            <button
                            {{-- wire:click="deleteDevice({{ $device->id }})" --}}
                                x-data=""
                                x-on:click.prevent="$dispatch('open-modal', 'delete-device-modal-{{$device->id}}')"
                                class="text-red-500 hover:text-red-700 ml-4">
                                <i class="fas fa-trash"></i>
                            </button>
                            <x-modal name="delete-device-modal-{{$device->id}}" :show="$errors->isNotEmpty()" focusable maxWidth="sm" class="p-6">
                                <form wire:submit.prevent="deleteDevice({{ $device->id }})" class="p-6">
                                <div class="mb-4">
                                    <p class="text-sm font-medium text-gray-900">
                                        Are you sure you want to delete device: "{{ $device->name }}"?
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
                                    {{-- wire:click="deleteDevice({{ $device->id }})"  --}}
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
                @endforeach {{-- End of devices loop --}}
                @else
                <tr align="center">
                    <td class="border border-l-0 border-r-0 px-4 py-2" colspan="6">No Device Found. <a href="#">Create
                            One!</a> </td>
                </tr>
                @endif
            </tbody>
        </table>
    </div>


</div>
