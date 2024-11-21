<?php

use function Livewire\Volt\{state};
use App\Models\Customer; // Add this line to import the Customer model

// Fetch the latest customers
state('latestCustomers', Customer::getLatestCustomers());

?>

<div class="card">

    <div class="card-body">
        <div class="flex flex-row justify-between items-center">
            <h1 class="font-extrabold text-lg">New Customers</h1>
            <a href="#" class="btn-gray text-sm">view all</a>
        </div>

        <table class="table-auto w-full mt-5 text-right">

            <thead>
                <tr>
                    <td class="py-4 font-extrabold text-sm text-left">Customer Name</td>
                    <td class="py-4 font-extrabold text-sm">Phone</td>
                    {{-- <td class="py-4 font-extrabold text-sm">Address</td> --}}
                    <td class="py-4 font-extrabold text-sm">Action</td>
                </tr>
            </thead>

            <tbody>
                @if (count($this->latestCustomers)>0)
                @foreach($this->latestCustomers as $customer) <!-- Loop through the latest customers -->
                <tr class="">
                    <td class="py-4 text-sm text-gray-600 flex flex-row items-center text-left">
                        {{ $customer->name }} <!-- Display customer name -->
                    </td>
                    <td class="py-4 text-xs text-gray-600">{{ $customer->phone }}</td> <!-- Display customer phone -->
                    {{-- <td class="py-4 text-xs text-gray-600">{{ $customer->address }}</td> <!-- Display customer address --> --}}
                    <td class="py-4 text-xs text-gray-600"></td>
                </tr>
                @endforeach
                @else
                <tr align="center">
                    <td colspan="4" class="text-lg text-gray-600"> No customers yet</td>
                </tr>

                @endif
                <!-- end item -->

            </tbody>

        </table>

    </div>
</div>
