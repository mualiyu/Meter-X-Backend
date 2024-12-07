<?php

namespace App\Http\Controllers;

use App\Models\ConsumptionHistory;
use App\Models\Device;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use PhpMqtt\Client\Facades\MQTT;

class DeviceController extends Controller
{
    public function test_mqtt()
    {
        // MQTT::publish('mukeey/test', 'Hello World!');
        // return true;

        // $mqtt = MQTT::connection();
        // $mqtt->publish('mukeey/test', 'Test', 2, true);
        // $mqtt->publish('mukeey/test', 'Test thing', 2, true);
        // $mqtt->publish('mukeey/test', 'Test thing again', 2, true);
        // $mqtt->loop(true, true);

        $mqtt = MQTT::connection();
        $mqtt->subscribe('meterX/MX_8476e59d5c5cdaacfd9cbb57c8c661c1/data', function (string $topic, string $message) {
            // echo sprintf('Received QoS level 1 message on topic [%s]: %s', $topic, $message);
            echo $message;
        }, 1);
        $mqtt->loop(true);

        return true;

        // meterX/MX_8476e59d5c5cdaacfd9cbb57c8c661c1/data
    }

    function get_device_data($device_id, Request $request)
    {
        // $validator = Validator::make($request->all(), [
        //     'voltage' => 'required',
        //     'current' => 'required',
        //     'power' => 'required',
        //     'light' => 'required'
        // ]);

        // if ($validator->fails()) {
        //     return response()->json([
        //         "status" => false,
        //         "message" => $validator->errors()
        //     ], 422);
        // }

        $request->validate([
            'voltage' => 'required',
            'current' => 'required',
            'power' => 'required',
            'light' => 'required'
        ]);

        $device = Device::where('device_id', $device_id)->firstOrFail();

        if ($device) {
            $device->consumptionHistory()->create([
                'voltage' => $request->voltage,
                'current' => $request->current,
                'power' => $request->power,
                'is_power_active' => $request->is_power_active,
                'date' => now()->format('Y-m-d'),
                'time' => now()->format('H:i:s'),
            ]);
            return response()->json([
                "status" => true,
                "data" => $device,
            ], 200);
        }else{
            return response()->json([
                "status" => true,
                "data" => $device,
            ], 422);
        }

        // $data = ConsumptionHistory::create([
        //     'customer_id' => $customer->id,
        //     'service_provider' => $request->service_provider,
        //     'phone' => $request->phone,
        //     'amount' => $request->amount,
        //     'status' => '0',
        //     'ref_id' => $request->requestId,
        //     // 'data' => "",
        // ]);
    }
}
