<?php

namespace App\Services;

use App\Models\Customer;
use App\Models\Electricity;
use App\Models\System;

class VTPass
{
    // Define any required properties
    protected $api_url;
    protected $api_key;
    protected $public_key;
    protected $secret_key;

    /**
     * Constructor for dependency injection
     *
     */
    public function __construct()
    {
        $this->api_url = System::first()->api_service->api_endpoint;
        $this->api_key = System::first()->api_service->api_key;
        $this->public_key = System::first()->api_service->api_public_key;
        $this->secret_key = System::first()->api_service->api_secret_key;
    }

    /**
     * Perform some action
     *
     * @param array $data
     * @return mixed
     */
    public function verify_meter($billersCode, $serviceID, $type)
    {
        $url = $this->api_url . "/merchant-verify";

        $data = [
            'billersCode' => $billersCode,
            'serviceID' => $serviceID,
            'type' => $type
        ];
        $headers = [
            'Content-Type' => 'application/json',
            'api-key' => $this->api_key,
            // 'public-key' => $this->public_key,
            'secret-key' => $this->secret_key,
        ];

        $client = new \GuzzleHttp\Client();
        $response = $client->post($url, [
            'headers' => $headers,
            'json' => $data
        ]);

        $body = $response->getBody();

        $result = json_decode($body, true);
        // return $result['content']['WrongBillersCode'];

        if ($result['code'] === "000") {
            // return $result;
            if (!isset($result['content']['error'])) {
                $res = [
                    'status' => true,
                    'data' => $result['content'],
                    // 'data' => [
                    //     'customerName' => $result['content']['Customer_Name'],
                    //     'address' => $result['content']['Address'],
                    //     'meterNo' => $result['content']['MeterNumber'],
                    //     'meterType' => $result['content']['Meter_Type'],
                    //     'lastPurchaseDays' => $result['content']['Last_Purchase_Days'],
                    // ]
                ];
                return $res;
            } else {
                $res = [
                    'status' => false,
                    'error' => $result['content']['error'],
                ];
                return $res;
            }
        } elseif ($result['code'] === "012") {
            $res = [
                'status' => false,
                'error' => $result['response_description'],
            ];
            return $res;
        } else {
            $res = [
                'status' => false,
                'error' => "Failed, Try again later.",
            ];
            return $res;
        }
    }


    public function meter_purchase($requestId, $serviceID, $billersCode, $variationCode, $amount, $phone)
    {
        $url = $this->api_url . "/pay";

        $data = [
            'request_id' => $requestId,
            'serviceID' => $serviceID,
            'billersCode' => $billersCode,
            'variation_code' => $variationCode,
            'amount' => $amount,
            'phone' => $phone,
        ];
        $headers = [
            'Content-Type' => 'application/json',
            'api-key' => $this->api_key,
            // 'public-key' => $this->public_key,
            'secret-key' => $this->secret_key,
        ];

        $client = new \GuzzleHttp\Client();
        $response = $client->post($url, [
            'headers' => $headers,
            'json' => $data
        ]);

        $body = $response->getBody();

        $result = json_decode($body, true);

        if ($result['code'] === "000") {
            // return $result;
            if (!isset($result['content']['error'])) {
                $res = [
                    'status' => true,
                    'data' => $result,
                ];
                return $res;
            } else {
                $res = [
                    'status' => false,
                    'error' => $result['content']['error'],
                ];
                return $res;
            }
        } elseif ($result['code'] === "012") {
            $res = [
                'status' => false,
                'error' => $result['response_description'],
            ];
            return $res;
        } else {
            $res = [
                'status' => false,
                'error' => "Failed, Try again later.",
            ];
            return $res;
        }
        // return $result;
    }

    public function verify_transaction($requestId)
    {
        $url = $this->api_url . "/requery";

        $data = [
            'request_id' => $requestId,
        ];
        $headers = [
            'Content-Type' => 'application/json',
            'api-key' => $this->api_key,
            // 'public-key' => $this->public_key,
            'secret-key' => $this->secret_key,
        ];

        $client = new \GuzzleHttp\Client();
        $response = $client->post($url, [
            'headers' => $headers,
            'json' => $data
        ]);

        $body = $response->getBody();

        $result = json_decode($body, true);

        // return $result;

        if ($result['code'] === "000") {
            // return $result;
            if (!isset($result['content']['error'])) {
                $res = [
                    'status' => true,
                    'data' => $result,
                ];
                return $res;
            } else {
                $res = [
                    'status' => false,
                    'error' => $result['content']['error'],
                ];
                return $res;
            }
        } elseif ($result['code'] === "012") {
            $res = [
                'status' => false,
                'error' => $result['response_description'],
            ];
            return $res;
        } elseif ($result['code'] === "015") {
            $res = [
                'status' => false,
                'error' => $result['response_description'],
            ];
            return $res;
        }
        else {
            $res = [
                'status' => false,
                'error' => "Failed, Try again later.",
            ];
            return $res;
        }
    }

}
