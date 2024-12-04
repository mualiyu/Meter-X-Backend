<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Mail;
use App\Mail\WelcomeEmail;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        // Validate the request
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:customers',
            'password' => 'required|string|min:8|confirmed',
            'phone' => 'required|string|max:15|unique:customers',
            // Add other fields as necessary
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // Create the customer
        $customer = Customer::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'phone' => $request->phone,
            // Add other fields as necessary
        ]);

        // Send welcome email
        try {
            Mail::to($customer->email)->send(new WelcomeEmail($customer));
        } catch (\Exception $e) {
            // Log the error but don't stop the registration process
            \Log::error('Failed to send welcome email: ' . $e->getMessage());
        }

        return response()->json([
            'status' => true,
            'message' => 'Customer registered successfully',
            'customer' => $customer
        ], 201);
    }

    public function login(Request $request)
    {
        // Validate the request
        $validator = Validator::make($request->all(), [
            'username' => 'required',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                "status" => false,
                "message" => $validator->errors()
            ], 422);
        }

        $customer = Customer::where('email', $request->username)->first();

        if (!$customer) {
            $customer = Customer::where('phone', $request->username)->first();
        }

        if (!$customer || !Hash::check($request->password, $customer->password)) {
            return response()->json([
                "status" => false,
                'message' => 'Invalid credentials'
            ], 401);
        }

        $can = "Customer";
        $customer->is_online = "1";
        $customer->save();
        $token = $customer->createToken('customerAuthToken', [$can])->plainTextToken;

        return response()->json([
            "status" => true,
            'customer' => $customer,
            'token' => $token
        ], 200);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        $request->user()->is_online = "0";
        $request->user()->save();

        return response()->json([
            'status' => true,
            'message' => 'Logged out successfully'
        ], 200);
    }

    public function sendResetLinkEmail(Request $request)
    {
        // Validate the request
        $validator = Validator::make($request->all(), [
            'email' => 'required|email'
        ]);

        if ($validator->fails()) {
            return response()->json([
                "status" => false,
                "message" => $validator->errors()
            ], 422);
        }

        // Send the password reset link
        $response = Password::sendResetLink($request->only('email'));

        return $response == Password::RESET_LINK_SENT
            ? response()->json([
                'status' => true,
                'message' => 'Reset link sent to your email.'
            ], 200)
            : response()->json([
                'status' => false,
                'message' => 'Unable to send reset link.'
            ], 500);
    }

    public function reset(Request $request)
    {
        // Validate the request
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string|min:8|confirmed',
            'token' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                "status" => false,
                "message" => $validator->errors()
            ], 422);
        }

        // Reset the password
        $response = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($customer, $password) {
                $customer->password = Hash::make($password);
                $customer->save();
            }
        );

        return $response == Password::PASSWORD_RESET
            ? response()->json([
                'status' => true,
                'message' => 'Password has been reset.',
            ], 200)
            : response()->json([
                'status' => false,
                'message' => 'Unable to reset password.',
            ], 500);
    }

    public function getProfile(Request $request)
    {
        // Return the authenticated customer's profile
        return response()->json([
            'status' => true,
            'user' => $request->user(),
        ], 200);
    }

    public function updateProfile(Request $request)
    {
        // Validate the request
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            // 'email' => 'sometimes|required|string|email|max:255|unique:customers,email,' . $request->user()->id,
            'phone' => 'required|string|max:15',
            'address' => 'required|string',
            // Add other fields as necessary
        ]);

        if ($validator->fails()) {
            // return response()->json([
            //     'status' => false,
            //     'message' => $validator->errors()], 422);
            return response()->json($validator->errors(), 422);
        }

        // Update the customer's profile
        $customer = $request->user();

        // Save the updated customer
        Customer::where('id', $customer->id)->update([
            "name" => $request->name,
            "address" => $request->address,
            "phone" => $request->phone,
        ]);

        $customer = Customer::find($customer->id);

        return response()->json([
            'status' => true,
            'message' => 'Profile updated successfully',
            'customer' => $customer,
        ], 200);
    }
}
