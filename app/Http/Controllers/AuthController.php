<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\UserModel;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'email' => 'required|email',
                'password' => 'required'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'Failed',
                    'message' => $validator->errors()->all(),
                    'data' => []
                ], 422);
            }

            $user = UserModel::where('email', $request->email)->first();

            if ($user != null) {
                if (password_verify($request->password, $user->password)) {
                    $token = $user->createToken('auth_token')->plainTextToken;

                    return response()->json([
                        'status' => 'Success',
                        'message' => 'Login successful',
                        'data' => [
                            'user' => $user,
                            'token' => $token
                        ]
                    ], 200);
                } else {
                    return response()->json([
                        'status' => 'Failed',
                        'message' => 'Password is incorrect',
                        'data' => []
                    ], 401);
                }
            } else {
                return response()->json([
                    'status' => 'Failed',
                    'message' => 'User not found',
                    'data' => []
                ], 401);
            }
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to login',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
