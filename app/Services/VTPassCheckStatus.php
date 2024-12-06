<?php

namespace App\Services;

use App\Models\Customer;
use App\Models\Electricity;
use App\Models\System;

class VTPassCheckStatus
{
    // Define any required properties

    /**
     * Constructor for dependency injection
     *
     */
    public function __construct()
    {

    }

    public function check($result)
    {

        if (isset($result['code'])) {
            if ($result['code'] === "000") {
                $res = [
                    'status' => true,
                    'error' => "TRANSACTION PROCESSED",
                ];
            } elseif ($result['code'] === "099") {
                    $res = [
                        'status' => false,
                        'error' => "TRANSACTION IS PROCESSING",
                    ];
            } elseif ($result['code'] === "001") {
                $res = [
                    'status' => false,
                    'error' => "TRANSACTION QUERY",
                ];
            } elseif ($result['code'] === "044") {
                $res = [
                    'status' => false,
                    'error' => "TRANSACTION RESOLVED",
                ];
            } elseif ($result['code'] === "091") {
                $res = [
                    'status' => false,
                    'error' => "TRANSACTION NOT PROCESSED",
                ];
            } elseif ($result['code'] === "016") {
                $res = [
                    'status' => false,
                    'error' => "TRANSACTION FAILED",
                ];
            } elseif ($result['code'] === "010") {
                $res = [
                    'status' => false,
                    'error' => "VARIATION CODE DOES NOT EXIST",
                ];
            } elseif ($result['code'] === "011") {
                $res = [
                    'status' => false,
                    'error' => "INVALID ARGUMENTS",
                ];
            } elseif ($result['code'] === "012") {
                $res = [
                    'status' => false,
                    'error' => "PRODUCT DOES NOT EXIST",
                ];
            } elseif ($result['code'] === "013") {
                $res = [
                    'status' => false,
                    'error' => "BELOW MINIMUM AMOUNT ALLOWED",
                ];
            } elseif ($result['code'] === "014") {
                $res = [
                    'status' => false,
                    'error' => "REQUEST ID ALREADY EXIST",
                ];
            } elseif ($result['code'] === "015") {
                $res = [
                    'status' => false,
                    'error' => "INVALID REQUEST ID",
                ];
            } elseif ($result['code'] === "017") {
                $res = [
                    'status' => false,
                    'error' => "ABOVE MAXIMUM AMOUNT ALLOWED",
                ];
            } elseif ($result['code'] === "018") {
                $res = [
                    'status' => false,
                    'error' => "LOW WALLET BALANCE",
                ];
            } elseif ($result['code'] === "019") {
                $res = [
                    'status' => false,
                    'error' => "LIKELY DUPLICATE TRANSACTION",
                ];
            } elseif ($result['code'] === "021") {
                $res = [
                    'status' => false,
                    'error' => "ACCOUNT LOCKED",
                ];
            } elseif ($result['code'] === "022") {
                $res = [
                    'status' => false,
                    'error' => "ACCOUNT SUSPENDED",
                ];
            } elseif ($result['code'] === "023") {
                $res = [
                    'status' => false,
                    'error' => "API ACCESS NOT ENABLE FOR USER",
                ];
            } elseif ($result['code'] === "024") {
                $res = [
                    'status' => false,
                    'error' => "ACCOUNT INACTIVE",
                ];
            } elseif ($result['code'] === "025") {
                $res = [
                    'status' => false,
                    'error' => "RECIPIENT BANK INVALID",
                ];
            } elseif ($result['code'] === "026") {
                $res = [
                    'status' => false,
                    'error' => "RECIPIENT ACCOUNT COULD NOT BE VERIFIED",
                ];
            } elseif ($result['code'] === "027") {
                $res = [
                    'status' => false,
                    'error' => "IP NOT WHITELISTED, CONTACT SUPPORT",
                ];
            } elseif ($result['code'] === "028") {
                $res = [
                    'status' => false,
                    'error' => "PRODUCT IS NOT WHITELISTED ON YOUR ACCOUNT",
                ];
            } elseif ($result['code'] === "030") {
                $res = [
                    'status' => false,
                    'error' => "BILLER NOT REACHABLE AT THIS POINT",
                ];
            } elseif ($result['code'] === "031") {
                $res = [
                    'status' => false,
                    'error' => "BELOW MINIMUM QUANTITY ALLOWED",
                ];
            } elseif ($result['code'] === "032") {
                $res = [
                    'status' => false,
                    'error' => "ABOVE MINIMUM QUANTITY ALLOWED",
                ];
            } elseif ($result['code'] === "034") {
                $res = [
                    'status' => false,
                    'error' => "SERVICE SUSPENDED",
                ];
            } elseif ($result['code'] === "035") {
                $res = [
                    'status' => false,
                    'error' => "SERVICE INACTIVE",
                ];
            } elseif ($result['code'] === "040") {
                $res = [
                    'status' => false,
                    'error' => "TRANSACTION REVERSAL",
                ];
            } elseif ($result['code'] === "083") {
                $res = [
                    'status' => false,
                    'error' => "SYSTEM ERROR",
                ];
            } elseif ($result['code'] === "085") {
                $res = [
                    'status' => false,
                    'error' => "IMPROPER REQUEST ID: DOES NOT CONTAIN DATE",
                ];
            } elseif ($result['code'] === "085") {
                $res = [
                    'status' => false,
                    'error' => "IMPROPER REQUEST ID: NOT PROPER DATE FORMAT – FIRST 8 CHARACTERS MUST BE DATE (TODAY’S DATE – YYYYMMDD)",
                ];
            } elseif ($result['code'] === "085") {
                $res = [
                    'status' => false,
                    'error' => "IMPROPER REQUEST ID: DATE NOT TODAY’S DATE – FIRST 8 CHARACTERS MUST BE TODAY’S DATE IN THIS FORMAT: YYYYMMDD",
                ];
            } else {
                $res = [
                    'status' => false,
                    'error' => "Failed, Try again.",
                ];
            }
        }else{
            $res = [
                'status' => false,
                'error' => "Failed, Try again. No code.",
            ];
        }

        return $res;
    }

}
