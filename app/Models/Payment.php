<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Yabacon\Paystack;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'amount',
        'customer_id',
        'payable_type', //Electricity, Airtime, or Data
        'payable_id',
        'payment_method',
        'status',
        'reference',
        'description',
        'paystack_reference',
        'paystack_payment_url',
        'payment_data',
    ];

    protected $casts = [
        'payment_data' => 'array',
    ];

    // Polymorphic relationship to handle different payment types
    public function payable()
    {
        return $this->morphTo();
    }

    // Relationship with customer
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function initializePaystackPayment()
    {
        $paystack = new Paystack(config('services.paystack.secret_key'));

        try {
            $response = $paystack->transaction->initialize([
                'amount' => $this->amount * 100, // Paystack amount in kobo
                'email' => $this->customer->email,
                'reference' => $this->reference,
                'callback_url' => route('paystack.callback'),
                'metadata' => [
                    'payment_id' => $this->id,
                    'customer_id' => $this->customer_id,
                    'payable_type' => $this->payable_type,
                    'payable_id' => $this->payable_id,
                ]
            ]);

            $this->update([
                'paystack_reference' => $response['data']['reference'],
                'paystack_payment_url' => $response['data']['authorization_url'],
                'payment_data' => $response['data']
            ]);

            return $response['data']['authorization_url'];

        } catch (\Exception $e) {
            // \Log::error('Paystack payment initialization failed: ' . $e->getMessage());
            throw new \Exception('Payment initialization failed');
        }
    }

    public function verifyPaystackPayment()
    {
        $paystack = new Paystack(config('services.paystack.secret_key'));

        // throw new $this->reference;

        try {
            $response = $paystack->transaction->verify([
                'reference' => $this->reference
            ]);

            if ($response) {
                # code...
                if ($response['data']['status'] === 'success') {
                    $this->update([
                        'status' => 'completed',
                        'payment_data' => array_merge(
                            $this->payment_data ?? [],
                            ['verification' => $response['data']]
                        )
                    ]);

                    // Update the related service status
                    $this->payable->update(['status' => '1']);

                    return true;
                }
                return false;
            }
            return false;

        } catch (\Exception $e) {
            \Log::error('Paystack payment verification failed: ' . $e->getMessage());
            // throw new \Exception('Payment verification failed');
            return false;
        }
    }
}
