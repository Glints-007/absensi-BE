<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use App\Models\User;
use App\Models\Office;
use App\Mail\SendForgotPassword;
use Illuminate\Support\Facades\Mail;

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

            if($user->status == 'rejected'){
                $respon = [
                    'status' => 'failed',
                    'msg' => "Your requested account has been rejected",
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
        if(!$user){
            $respon = [
                'status' => 'error',
                'msg' => "Auth user doesn't exist. Please check your token",
                'errors' => null,
                'content' => null,
            ];
            return response()->json($respon, 200);
        }
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
        $validatedData = \Validator::make($request->all(), [
            'name' => 'required|string',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
        ]);

        if ($validatedData->fails()) {
            $respon = [
                'status' => 'error',
                'msg' => 'Validator error',
                'errors' => $validatedData->errors(),
                'content' => null,
            ];
            return response()->json($respon, 200);
        }

        $user = User::create([
            'uid' => Str::uuid(),
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'office_id' => Office::all()->first()->id,
            'status' => 'unverified',
            'role' => 'user',
        ]);

        return response()->json([
            'status' => 'success',
            'msg' => 'Registered successfully. Please wait for your account to be verified',
            'errors' => null,
        ]);
    }

    public function forgot(Request $request)
    {
        $validatedData = \Validator::make($request->all(), [
            'email' => 'required|string|email',
        ]);

        if ($validatedData->fails()) {
            $respon = [
                'status' => 'error',
                'msg' => 'Validator error',
                'errors' => $validatedData->errors(),
                'content' => null,
            ];
            return response()->json($respon, 200);
        }

        $user = User::where('email', $request->email)->first();
        if (!$user) {
            $error_message = "Email doesn't exist";
            return response()->json(['success' => false, 'error' => ['email'=> $error_message]], 401);
        }

        $token = rand(100000,999999);
        // dd($token);
        DB::table('password_resets')->insert([
            'email' => $user->email,
            'token' => Hash::make($token),
            'created_at' => now(),
        ]);

        Mail::to($user->email)->send(new SendForgotPassword($token));

        return response()->json([
            'success' => true, 'data'=> ['message'=> 'A reset email has been sent! Please check your email.']
        ]);
    }

    public function reset(Request $request)
    {
        $validatedData = \Validator::make($request->all(), [
            'email' => 'required|string|email',
            'token' => 'required|min:6|max:6',
            'password' => 'required|string|min:8',
            'confirm_password' => 'required|string|min:8',
        ]);

        if ($validatedData->fails()) {
            $respon = [
                'status' => 'error',
                'msg' => 'Validator error',
                'errors' => $validatedData->errors(),
                'content' => null,
            ];
            return response()->json($respon, 200);
        }

        if($request->password != $request->confirm_password){
            $respon = [
                'status' => 'error',
                'msg' => 'Confirmation error',
                'errors' => "Password confirmation doesn't match the password",
                'content' => null,
            ];
            return response()->json($respon, 200);
        }

        $user = User::where('email', $request->email)->first();
        if (!$user) {
            $error_message = "Email doesn't exist";
            return response()->json(['success' => false, 'error' => ['email'=> $error_message]], 401);
        }

        $reset = DB::table('password_resets')->where('email', $request->email)->latest()->first();

        if (! \Hash::check($request->token, $reset->token, [])) {
            $respon = [
                'status' => 'failed',
                'msg' => "OTP token doesn't match",
            ];
            return response()->json($respon, 200);
        }

        User::where('email', $request->email)->update([
            'password' => Hash::make($request->password),
        ]);

        DB::table('password_resets')->where([
            ['email', $request->email],
            ['token', $reset->token],
        ])->delete();

        $respon = [
            'status' => 'success',
            'msg' => 'Your password has been changed successfully',
            'errors' => null,
            'content' => null,
        ];
        return response()->json($respon, 200);
    }
}