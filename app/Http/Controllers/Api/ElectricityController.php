<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Electricity;
use App\Services\VTPass;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ElectricityController extends Controller
{
    public function getElectricityCompanies(Request $request)
    {
        // Define the electricity companies and their service IDs
        $companies = [
            'AEDC – Abuja Electric' => 'abuja-electric',
            'ABA – ABA Electric' => 'aba-electric',
            'IKEDC – Ikeja Electric' => 'ikeja-electric',
            'EKEDC – Eko Electricity' => 'eko-electric',
            'KEDCO – Kano Electricity' => 'kano-electric',
            'PHED – Port Harcourt Electric' => 'portharcourt-electric',
            'JED – Jos Electricity' => 'jos-electric',
            'IBEDC – Ibadan Electricity' => 'ibadan-electric',
            'KAEDCO – Kaduna Electric' => 'kaduna-electric',
            'EEDC – Enugu Electric' => 'enugu-electric',
            'BEDC – Benin Electric' => 'benin-electric',
            'YEDC – YOLA Electric' => 'yola-electric',
        ];

        // Return as a JSON response
        return response()->json([
            'status' => true,
            'message' => 'Electricity companies retrieved successfully.',
            'data' => $companies,
        ], 200);
    }

    public function verify_meter(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'meterNo' => 'required',
            'serviceID' => 'required',
            'type' => 'required',
            'phone' => 'nullable',
        ]);

        if ($validator->fails()) {
            return response()->json([
                "status" => false,
                "message" => $validator->errors()
            ], 422);
        }

        $vtpassService = new VTPass();
        $result = $vtpassService->verify_meter($request->meterNo, $request->serviceID, $request->type);

        if ($result) {
            if ($result['status']) {
                return response()->json([
                    "status" => true,
                    "message" => "Meter verification successful",
                    "data" => $result['data']
                ], 200);
            } else {
                return response()->json([
                    "status" => false,
                    "message" => $result['error'],
                ], 422);
            }
        } else {
            return response()->json([
                "status" => false,
                "message" => "Meter verification failed",
            ], 422);
        }
    }

    function request_purchase(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'serviceID' => 'required',
            'meterNo' => 'required',
            'type' => 'required',
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

        $request['requestId'] = date('YmdHis') . "MXEB";

        $electricity = Electricity::create([
            'customer_id' => $customer->id,
            'meter_no' => $request->meterNo,
            'meter_type' => $request->type,
            'service_provider' => $request->serviceID,
            'phone' => $request->phone,
            'amount' => $request->amount,
            'status' => '0',
            'ref_id' => $request->requestId,
        ]);

        if ($electricity) {
            $customer = $electricity->customer;

            // create payment record
            $electricity->payment()->create([
                'amount' => $request->amount,
                'customer_id' => $customer->id,
                'payment_method' => $request->payment_method,
                'status' => 'pending',
                'reference' => $request->requestId,
                'description' => 'Electricity bill payment'
            ]);

            return response()->json([
                "status" => true,
                "message" => "Successful, you can make payments now.",
                "data" => $electricity,
                "ref_id" => $electricity->ref_id,
            ], 200);
        } else {
            return response()->json([
                "status" => false,
                "message" => "Failed, Try again later.",
            ], 422);
        }
    }

    function verify_payment(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'ref_id' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                "status" => false,
                "message" => $validator->errors()
            ], 422);
        }
        $electricity = Electricity::where('ref_id', $request->ref_id)->firstOrFail();

        if ($electricity) {
            // test
            // $vtpassService = new VTPass();
            // $result = $vtpassService->meter_purchase($electricity->ref_id, $electricity->service_provider, $electricity->meter_no, $electricity->meter_type, $electricity->amount, $electricity->phone);
            // return $result;
            // End test

            $payment = $electricity->payment;
            if ($payment->verifyPaystackPayment()) {
                $e = Electricity::where('ref_id', $request->ref_id)->with('payment')->with('customer')->firstOrFail();

                $vtpassService = new VTPass();
                $result = $vtpassService->meter_purchase($e->ref_id, $e->service_provider, $e->meter_no, $e->meter_type, $e->amount, $e->phone);

                if ($result) {
                    if ($result['status']) {
                        $e->data = $result['data'];
                        $e->save();

                        return response()->json([
                            "status" => true,
                            "payment_status" => true,
                            "message" => "Successful, Payment has been successfully received and unit has been allocated to meter: $e->meter_no ",
                            "data" => $result['data']
                        ], 200);
                    } else {
                        $e->data = $result['error'];
                        $e->save();

                        return response()->json([
                            "status" => false,
                            "payment_status" => true,
                            "message" => "Payment successful, but unit has not been allocated yet, kindly wait for 30-Mins to 1-Hour, if you didn't get Email or SMS then contact our support team at +2348167236629.",
                        ], 422);
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

    public function purchase_payment(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'serviceID' => 'required',
            'meterNo' => 'required',
            'type' => 'required',
            'amount' => 'required',
            'phone' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                "status" => false,
                "message" => $validator->errors()
            ], 422);
        }

        $customer = $request->user();

        $request['requestId'] = date('YmdHis') . "MXEB";

        $vtpassService = new VTPass();
        $result = $vtpassService->meter_purchase($request->requestId, $request->serviceID, $request->meterNo, $request->type, $request->amount, $request->phone);

        if ($result) {

            if ($result['status']) {
                return response()->json([
                    "status" => true,
                    "message" => "Meter verification successful",
                    "data" => $result['data']
                ], 200);
            } else {
                return response()->json([
                    "status" => false,
                    "message" => $result['error'],
                ], 422);
            }
        } else {
            return response()->json([
                "status" => false,
                "message" => "Failed to get Token",
            ], 422);
        }
    }

    public function verify_transaction(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'ref_id' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                "status" => false,
                "message" => $validator->errors()
            ], 422);
        }

        $vtpassService = new VTPass();
        $result = $vtpassService->verify_transaction($request->ref_id);

        if ($result) {
            // return $result;
            if ($result['status']) {
                return response()->json([
                    "status" => true,
                    "message" => "Meter verification successful",
                    "data" => $result['data']
                ], 200);
            } else {
                return response()->json([
                    "status" => false,
                    "message" => $result['error'],
                ], 422);
            }
        } else {
            return response()->json([
                "status" => false,
                "message" => "Failed to get Token",
            ], 422);
        }
    }

    public function history(Request $request)
    {
        $customer = $request->user();

        if ($customer) {
            if (count($customer->electricity_history)>0) {
                return response()->json([
                    "status" => true,
                    "data" => $customer->electricity_history,
                    "message" => "Successful response",
                ], 200);
            }else{
                return response()->json([
                    "status" => false,
                    "message" => "Sorry you don't have any previously purchased electricity unit"
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
