<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');



Route::prefix('customers')->group(function () {
    Route::prefix('v1')->group(function () {

        Route::post('/register', '\App\Http\Controllers\Api\AuthController@register');
        Route::post('/login', '\App\Http\Controllers\Api\AuthController@login');
        Route::post('/password/forgot', '\App\Http\Controllers\Api\AuthController@sendResetLinkEmail');
        Route::post('/password/reset', '\App\Http\Controllers\Api\AuthController@reset');

        // Adding profile routes
        Route::prefix('profile')->middleware('auth:sanctum')->group(function () {
            Route::get('/', '\App\Http\Controllers\Api\AuthController@getProfile');
            Route::put('/update', '\App\Http\Controllers\Api\AuthController@updateProfile');
        });

        Route::post('/testing', function () {
            return response()->json([
                "status"=>true,
                "message" => "Testings"
            ]);
        });
    });
});

