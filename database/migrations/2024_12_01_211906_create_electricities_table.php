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
        Schema::create('electricities', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('customer_id');
            $table->string('meter_no');
            $table->string('meter_type');
            $table->string('service_provider');
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->decimal('amount', 8, 2);
            $table->decimal('unit', 8, 2)->nullable();
            $table->enum('status', [1, 0])->default(0);
            $table->string('ref_id')->nullable();
            $table->json('data')->nullable();
            // $table->string('meter_no');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('electricities');
    }
};
