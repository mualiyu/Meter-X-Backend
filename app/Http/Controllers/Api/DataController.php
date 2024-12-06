<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Data;
use App\Services\VTPass;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class DataController extends Controller
{
    public function getServiceProvider(Request $request)
    {
        $companies = [
            'MTN Data' => 'mtn-data',
            'Airtel Data' => 'airtel-data',
            'Glo Data' => 'glo-data',
            'Glo SME Data' => 'glo-sme-data',
            '9mobile Data' => 'etisalat-data',
            '9mobile SME Data' => '9mobile-sme-data',
            'Smile Network' => 'smile-direct',
            'Spectranet' => 'spectranet',
        ];

        // Return as a JSON response
        return response()->json([
            'status' => true,
            'message' => 'Service Providers Retrieved Successfully.',
            'data' => $companies,
        ], 200);
    }

    function get_variations(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'service_provider' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                "status" => false,
                "message" => $validator->errors()
            ], 422);
        }

        $customer = $request->user();

        $vtpassService = new VTPass();
        $result = $vtpassService->get_variation_codes($request->service_provider);

        if ($result) {
            if ($result['status']) {
                return response()->json([
                    "status" => true,
                    "message" => "Successful Response",
                    "data" => $result['data']['content']['varations'],
                ], 200);
            } else {
                return response()->json([
                    "status" => false,
                    "message" => $result['error'],
                ], 422);
            }
        }else {
            return response()->json([
                "status" => false,
                "message" => "Failed to get variations.",
            ], 422);
        }
    }

    public function request_data_purchase(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'service_provider' => 'required',
            'variation_code' => 'required',
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

        $request['requestId'] = date('YmdHis') . "MXDSUB";

        $data = Data::create([
            'customer_id' => $customer->id,
            'service_provider' => $request->service_provider,
            'variation_code' => $request->variation_code,
            'phone' => $request->phone,
            'amount' => $request->amount,
            'status' => '0',
            'ref_id' => $request->requestId,
            // 'data' => "",
        ]);

        if ($data) {
            $customer = $data->customer;

            // create payment record
            $data->payment()->create([
                'amount' => $request->amount,
                'customer_id' => $customer->id,
                'payment_method' => $request->payment_method,
                'status' => 'pending',
                'reference' => $request->requestId,
                'description' => 'Data Purchase Payment'
            ]);

            return response()->json([
                "status" => true,
                "message" => "Successful, you can make payments now.",
                "data" => $data,
                "ref_id" => $data->ref_id,
            ], 200);
        } else {
            return response()->json([
                "status" => false,
                "message" => "Failed, Try again later.",
            ], 422);
        }
    }

    public function verify_data_payment(Request $request)
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
        $data = Data::where('ref_id', $request->ref_id)->firstOrFail();

        if ($data) {
            // Test
                // $vtpassService = new VTPass();
                // $result = $vtpassService->buy_data($data->ref_id, $data->service_provider, $data->phone, $data->variation_code,  $data->amount, $data->phone);
                // return $result;
            // End Test

            $payment = $data->payment;

            if ($payment->verifyPaystackPayment()) {
                $d = Data::where('ref_id', $request->ref_id)->with('payment')->with('customer')->firstOrFail();

                $vtpassService = new VTPass();
                $result = $vtpassService->buy_data($d->ref_id, $d->service_provider, $d->phone, $d->variation_code,  $d->amount, $d->phone);

                if ($result) {
                    if ($result['status']) {
                        $d->data = $result['data'];
                        $d->save();

                        return response()->json([
                            "status" => true,
                            "payment_status" => true,
                            "message" => "Successful, Payment has been successfully received and data has been sent to Phone: $d->phone ",
                            "data" => $result['data']
                        ], 200);
                    } else {

                        $d->data = $result['error'];
                        $d->save();

                        return response()->json([
                            "status" => false,
                            "payment_status" => true,
                            "message" => "Payment successful, but data has not been sent yet, kindly wait for 30-Mins to 1-Hour, if you didn't receive itor get Email/SMS then contact our support team at +2348167236629.",
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
            if (count($customer->data_history)>0) {
                return response()->json([
                    "status" => true,
                    "data" => $customer->data_history,
                    "message" => "Successful response",
                ], 200);
            }else{
                return response()->json([
                    "status" => false,
                    "message" => "Sorry you haven't purchase any data bundle yet"
                ], 422);
            }
        }else{
            return response()->json([
                "status" => false,
                "message" => "No data/customer found, try again later!"
            ], 422);
        }

    }
}
