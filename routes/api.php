<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/test_mqtt', '\App\Http\Controllers\DeviceController@test_mqtt');
Route::post('/sensor/{device_id}/data', '\App\Http\Controllers\DeviceController@get_device_data');


Route::prefix('customers')->group(function () {
    Route::prefix('v1')->group(function () {

        Route::post('/register', '\App\Http\Controllers\Api\AuthController@register');
        Route::post('/login', '\App\Http\Controllers\Api\AuthController@login');
        Route::post('/password/forgot', '\App\Http\Controllers\Api\AuthController@sendResetLinkEmail');
        Route::post('/password/reset', '\App\Http\Controllers\Api\AuthController@reset');

        Route::middleware('auth:sanctum')->post('/logout', '\App\Http\Controllers\Api\AuthController@logout');

        // Adding profile routes
        Route::prefix('profile')->middleware('auth:sanctum')->group(function () {
            Route::get('/', '\App\Http\Controllers\Api\AuthController@getProfile');
            Route::put('/update', '\App\Http\Controllers\Api\AuthController@updateProfile');
        });

        // Electricity Bills
        Route::prefix('electricity-bill')->middleware('auth:sanctum')->group(function () {
            Route::post('/get-electricity-companies', '\App\Http\Controllers\Api\ElectricityController@getElectricityCompanies');
            Route::post('/verify-meter', '\App\Http\Controllers\Api\ElectricityController@verify_meter');
            Route::post('/request-purchase', '\App\Http\Controllers\Api\ElectricityController@request_purchase');
            Route::post('/verify-payment', '\App\Http\Controllers\Api\ElectricityController@verify_payment');

            Route::post('/history', '\App\Http\Controllers\Api\ElectricityController@history');

            Route::post('verify-transaction', '\App\Http\Controllers\Api\ElectricityController@verify_transaction');
            Route::post('/meter-purchase-unit', '\App\Http\Controllers\Api\ElectricityController@purchase_payment');
            // Route::post('/pay', '\App\Http\Controllers\Api\ElectricityController@updateProfile');
        });

        Route::prefix('airtime-vtu')->middleware('auth:sanctum')->group(function () {
            Route::post('/get-service-providers', '\App\Http\Controllers\Api\AirtimeController@getServiceProvider');
            Route::post('/request-airtime-purchase', '\App\Http\Controllers\Api\AirtimeController@request_airtime_purchase');
            Route::post('/verify-airtime-payment', '\App\Http\Controllers\Api\AirtimeController@verify_airtime_payment');

            Route::post('/history', '\App\Http\Controllers\Api\AirtimeController@history');
        });

        Route::prefix('data-subscription')->middleware('auth:sanctum')->group(function () {
            Route::post('/get-service-providers', '\App\Http\Controllers\Api\DataController@getServiceProvider');
            Route::post('/get-variations', '\App\Http\Controllers\Api\DataController@get_variations');
            Route::post('/request-data-purchase', '\App\Http\Controllers\Api\DataController@request_data_purchase');
            Route::post('/verify-data-payment', '\App\Http\Controllers\Api\DataController@verify_data_payment');


            Route::post('/history', '\App\Http\Controllers\Api\DataController@history');
        });

        Route::post('/testing', function () {
            return response()->json([
                "status"=>true,
                "message" => "Testings"
            ]);
        });
    });
});

