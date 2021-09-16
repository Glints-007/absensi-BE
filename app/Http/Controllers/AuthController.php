<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use App\Models\User;

class AuthController extends Controller
{
    public function login(Request $request) {
        $validate = \Validator::make($request->all(), [
            'email' => 'required',
            'password' => 'required',
        ]);

        if ($validate->fails()) {
            $respon = [
                'status' => 'error',
                'msg' => 'Validator error',
                'errors' => $validate->errors(),
                'content' => null,
            ];
            return response()->json($respon, 200);
        } else {
            $user = User::where('email', $request->email)->first();
            if(!$user){
                $respon = [
                    'status' => 'error',
                    'msg' => "Email doesn't exist",
                ];
                return response()->json($respon, 200);
            }
            if (! \Hash::check($request->password, $user->password, [])) {
                $respon = [
                    'status' => 'failed',
                    'msg' => "Password doesn't match",
                ];
                return response()->json($respon, 200);
            }
            if($user->status == 'unverified'){
                $respon = [
                    'status' => 'failed',
                    'msg' => "Your account has not been verified",
                ];
                return response()->json($respon, 200);
            }

            $tokenResult = $user->createToken('token-auth')->plainTextToken;
            $respon = [
                'status' => 'success',
                'msg' => 'Login successfully',
                'errors' => null,
                'content' => [
                    'status_code' => 200,
                    'access_token' => $tokenResult,
                    'token_type' => 'Bearer',
                    'office_id' => $user->office_id,
                    'role' => $user->role,
                ]
            ];
            return response()->json($respon, 200);
        }
    }

    public function logout(Request $request) {
        $user = $request->user();
        $user->currentAccessToken()->delete();
        $respon = [
            'status' => 'success',
            'msg' => 'Logout successfully',
            'errors' => null,
            'content' => null,
        ];
        return response()->json($respon, 200);
    }

    public function register(Request $request)
    {
        $validatedData = $request->validate([
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $validatedData['email'],
            'password' => Hash::make($validatedData['password']),
            'is_admin' => '0',
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'status' => 'success',
            'msg' => 'Registered successfully',
            'errors' => null,
            'access_token' => $token,
            'token_type' => 'Bearer',
        ]);
    }

    public function forgot(Request $request)
    {
        $user = User::where('email', $request->email)->first();
        if (!$user) {
            $error_message = "Your email address was not found.";
            return response()->json(['success' => false, 'error' => ['email'=> $error_message]], 401);
        }

        try {
            Password::sendResetLink(
                $request->only('email')
            );

        } catch (\Exception $e) {
            //Return with error
            $error_message = $e->getMessage();
            return response()->json(['success' => false, 'error' => $error_message], 401);
        }

        return response()->json([
            'success' => true, 'data'=> ['message'=> 'A reset email has been sent! Please check your email.']
        ]);
    }
}