<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('devices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->nullable();
            $table->string('device_name');
            $table->string('meter_id')->unique();
            $table->string('is_online')->nullable()->default('0');
            $table->enum('status', ['in_stock', 'purchased', 'damaged']);
            $table->enum('type', ['household', 'industrial']);
            $table->decimal('price', 8, 2)->nullable();
            $table->enum('stock_status', ['available', 'sold'])->default('available');
            $table->enum('is_linked', [1, 0])->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('devices');
    }
};
