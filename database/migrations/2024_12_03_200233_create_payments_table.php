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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->decimal('amount', 10, 2);
            $table->unsignedBigInteger('customer_id')->nullable();
            $table->morphs('payable');
            $table->string('payment_method');
            $table->string('status');
            $table->string('reference')->unique();
            $table->string('description')->nullable();
            $table->string('paystack_reference')->nullable();
            $table->string('paystack_payment_url')->nullable();
            $table->json('payment_data')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
