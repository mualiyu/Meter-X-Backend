<?php

use function Livewire\Volt\{state};

//

?>

{{-- <div class="card">

    <div class="card-body">
        <h2 class="font-bold text-lg mb-10">Recent Orders</h2>

    <!-- start a table -->
    <table class="table-fixed w-full">

        <!-- table head -->
        <thead class="text-left">
            <tr>
                <th class="w-1/2 pb-10 text-sm font-extrabold tracking-wide">customer</th>
                <th class="w-1/4 pb-10 text-sm font-extrabold tracking-wide text-right">Device</th>
                <th class="w-1/4 pb-10 text-sm font-extrabold tracking-wide text-right">price</th>
                <th class="w-1/4 pb-10 text-sm font-extrabold tracking-wide text-right">status</th>
            </tr>
        </thead>
        <!-- end table head -->

        <!-- table body -->
        <tbody class="text-left text-gray-600">

            <!-- item -->
            <tr>
                <!-- name -->
                <th class="w-1/2 mb-4 text-xs font-extrabold tracking-wider flex flex-row items-center w-full">

                    <p class="ml-3 name-1">user name</p>
                </th>
                <!-- name -->

                <!-- product -->
                <th class="w-1/4 mb-4 text-xs font-extrabold tracking-wider text-right">Smart Energy meter (Wifi)</th>
                <!-- product -->

                <!-- invoice -->
                <th class="w-1/4 mb-4 text-xs font-extrabold tracking-wider text-right">50,000</th>
                <!-- invoice -->


                <!-- status -->
                <th class="w-1/4 mb-4 text-xs font-extrabold tracking-wider text-right">shipped</th>
                <!-- status -->

            </tr>
            <!-- item -->

        </tbody>
        <!-- end table body -->

    </table>
    <!-- end a table -->
    </div>

</div> --}}

<div class="card col-span-2 xl:col-span-1">
    <div class="card-header">Recent Sales</div>

    <table class="table-auto w-full text-left">
        <thead>
            <tr>
                <th class="px-4 py-2 border-r"></th>
                <th class="px-4 py-2 border-r">product</th>
                <th class="px-4 py-2 border-r">price</th>
                <th class="px-4 py-2 border-r">date</th>
                <th class="px-4 py-2">action</th>
            </tr>
        </thead>
        <tbody class="text-gray-600">

            <tr>
                <td class="border border-l-0 px-4 py-2 text-center text-green-500"><i class="fad fa-circle"></i></td>
                <td class="border border-l-0 px-4 py-2">Lightning to USB-C Adapter Lightning.</td>
                <td class="border border-l-0 px-4 py-2"><span class="num-2"></span></td>
                <td class="border border-l-0 px-4 py-2"><span class="num-2"></span> minutes ago</td>
                <td class="border border-l-0 border-r-0 px-4 py-2">
                    <a href="#">View</a>
                </td>
            </tr>
            <tr>
                <td class="border border-l-0 px-4 py-2 text-center text-yellow-500"><i class="fad fa-circle"></i></td>
                <td class="border border-l-0 px-4 py-2">Apple iPhone 8.</td>
                <td class="border border-l-0 px-4 py-2"><span class="num-2"></span></td>
                <td class="border border-l-0 px-4 py-2"><span class="num-2"></span> minutes ago</td>
                <td class="border border-l-0 border-r-0 px-4 py-2">
                    <a href="#">View</a>
                </td>
            </tr>


        </tbody>
    </table>
</div>

