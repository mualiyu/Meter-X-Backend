<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Airtime;
use App\Services\VTPass;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AirtimeController extends Controller
{
    public function getServiceProvider(Request $request)
    {
        $companies = [
            'MTN' => 'mtn',
            'Airtel' => 'airtel',
            'Glo' => 'glo',
            '9mobile' => 'etisalat',
        ];

        // Return as a JSON response
        return response()->json([
            'status' => true,
            'message' => 'Service Providers Retrieved Successfully.',
            'data' => $companies,
        ], 200);
    }

    public function request_airtime_purchase(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'service_provider' => 'required',
            'amount' => 'required',
            'phone' => 'required',
            'payment_method' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                "status" => false,
                "message" => $validator->errors()
            ], 422);
        }

        $customer = $request->user();

        $request['requestId'] = date('YmdHis') . "MXAVTU";

        $airtime = Airtime::create([
            'customer_id' => $customer->id,
            'service_provider' => $request->service_provider,
            'phone' => $request->phone,
            'amount' => $request->amount,
            'status' => '0',
            'ref_id' => $request->requestId,
            // 'data' => "",
        ]);

        if ($airtime) {
            $customer = $airtime->customer;

            // create payment record
            $airtime->payment()->create([
                'amount' => $request->amount,
                'customer_id' => $customer->id,
                'payment_method' => $request->payment_method,
                'status' => 'pending',
                'reference' => $request->requestId,
                'description' => 'Airtime VTU Payment'
            ]);

            return response()->json([
                "status" => true,
                "message" => "Successful, you can make payments now.",
                "data" => $airtime,
                "ref_id" => $airtime->ref_id,
            ], 200);
        } else {
            return response()->json([
                "status" => false,
                "message" => "Failed, Try again later.",
            ], 422);
        }
    }


    public function verify_airtime_payment(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'ref_id' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                "status" => false,
                "payment_status" => false,
                "message" => $validator->errors()
            ], 422);
        }
        $airtime = Airtime::where('ref_id', $request->ref_id)->firstOrFail();

        if ($airtime) {
            // Test
                // $vtpassService = new VTPass();
                // $result = $vtpassService->buy_airtime($airtime->ref_id, $airtime->service_provider, $airtime->amount, $airtime->phone);
                // return $result;
            // End Test

            $payment = $airtime->payment;
            if ($payment->verifyPaystackPayment()) {
                $a = Airtime::where('ref_id', $request->ref_id)->with('payment')->with('customer')->firstOrFail();

                $vtpassService = new VTPass();
                $result = $vtpassService->buy_airtime($a->ref_id, $a->service_provider, $a->amount, $a->phone);

                if ($result) {
                    if ($result['status']) {
                        $a->data = $result['data'];
                        $a->save();

                        return response()->json([
                            "status" => true,
                            "payment_status" => true,
                            "message" => "Successful, Payment has been successfully received and airtime has been sent to Phone: $a->phone ",
                            "data" => $result['data']
                        ], 200);
                    } else {

                        $a->data = $result['error'];
                        $a->save();

                        return response()->json([
                            "status" => false,
                            "payment_status" => true,
                            "message" => "Payment successful, but airtime has not been sent to phone yet, kindly wait for 30-Mins to 1-Hour, if you didn't receive itor get Email/SMS then contact our support team at +2348167236629.",
                        ], 200);
                    }
                }
            } else {
                return response()->json([
                    "status" => false,
                    "payment_status" => false,
                    "message" => "Failed to verify payment, Please try again."
                ], 422);
            }
        } else {
            return response()->json([
                "status" => false,
                "message" => "No data found, try again later!"
            ], 422);
        }
    }

    public function history(Request $request)
    {
        $customer = $request->user();

        if ($customer) {
            if (count($customer->airtime_history)>0) {
                return response()->json([
                    "status" => true,
                    "data" => $customer->airtime_history,
                    "message" => "Successful response",
                ], 200);
            }else{
                return response()->json([
                    "status" => false,
                    "message" => "Sorry you don't have any previously purchased Airtime"
                ], 422);
            }
        }else{
            return response()->json([
                "status" => false,
                "message" => "No data found, try again later!"
            ], 422);
        }

    }
}
