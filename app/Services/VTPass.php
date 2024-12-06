<?php

namespace App\Services;

use App\Models\Customer;
use App\Models\Electricity;
use App\Models\System;
use App\Services\VTPassCheckStatus;

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

    // Meter Purchase Section
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

        $VTPassCheckStatus = new VTPassCheckStatus();
        $check = $VTPassCheckStatus->check($result);
        if ($check['status'] == true) {
            $res = [
                'status' => true,
                'data' => $result['content'],
            ];
            return $res;
        } else {
            return $check;
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

        $VTPassCheckStatus = new VTPassCheckStatus();
        $check = $VTPassCheckStatus->check($result);
        if ($check['status'] == true) {
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
        } else {
            return $check;
        }
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
        $VTPassCheckStatus = new VTPassCheckStatus();
        $check = $VTPassCheckStatus->check($result);
        if ($check['status'] == true) {
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
        } else {
            return $check;
        }
    }


    // Airtime Purchase Section
    public function buy_airtime($request_id, $serviceID, $amount, $phone)
    {
        $url = $this->api_url . "/pay";

        $data = [
            'request_id' => $request_id,
            'serviceID' => $serviceID,
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

        // return $result;

        $VTPassCheckStatus = new VTPassCheckStatus();
        $check = $VTPassCheckStatus->check($result);
        if ($check['status'] == true) {
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
        } else {
            return $check;
        }
    }


    // Data Purchase Section
    public function get_variation_codes($serviceID)
    {
        $url = $this->api_url . "/service-variations";

        $data = [
            'serviceID' => $serviceID,
        ];
        $headers = [
            'Content-Type' => 'application/json',
            'api-key' => $this->api_key,
            'public-key' => $this->public_key,
            // 'secret-key' => $this->secret_key,
        ];

        $client = new \GuzzleHttp\Client();
        $response = $client->get($url, [
            'headers' => $headers,
            'json' => $data
        ]);

        $body = $response->getBody();

        $result = json_decode($body, true);

        // return $result;

        if (!isset($result['code'])) {
            if ($result['response_description'] === "000") {
                // return $result;
                if (!isset($result['content']['errors'])) {
                    $res = [
                        'status' => true,
                        'data' => $result,
                    ];
                    return $res;
                } else {
                    $res = [
                        'status' => false,
                        'error' => $result['content']['errors'],
                    ];
                    return $res;
                }
            } else {
                $res = [
                    'status' => false,
                    'error' => "Failed, Try again later.",
                ];
                return $res;
            }
        } else {
            $VTPassCheckStatus = new VTPassCheckStatus();
            $check = $VTPassCheckStatus->check($result);
            if ($check['status'] == true) {
                $res = [
                    'status' => true,
                    'data' => $result,
                ];
                return $res;
            } else {
                return $check;
            }
        }
    }

    public function buy_data($request_id, $serviceID, $billersCode, $variation_code, $amount, $phone)
    {
        $url = $this->api_url . "/pay";

        $data = [
            'request_id' => $request_id,
            'serviceID' => $serviceID,
            'billersCode' => $billersCode,
            'variation_code' => $variation_code,
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

        // return $result;

        $VTPassCheckStatus = new VTPassCheckStatus();
        $check = $VTPassCheckStatus->check($result);
        if ($check['status'] == true) {
            $res = [
                'status' => true,
                'data' => $result['content'],
            ];
            return $res;
        } else {
            return $check;
        }
    }


    // TV shows Purchase Section

}
