<?php

use App\Models\User;
use Illuminate\Http\Request;

function role_check(Request $request){
    if($request->user()->role != 'admin'){
        $respon = [
            'status' => 'error',
            'msg' => 'authorization error',
            'errors' => 'You are not authorized to do this command',
            'content' => null,
        ];
        return response()->json($respon, 403);
    }
}

function status_check(User $user){
    if($user->status == 'verified'){
        $respon = [
            'status' => 'error',
            'msg' => 'verification error',
            'errors' => 'User account has been verified. No changes allowed',
            'content' => null,
        ];
        return response()->json($respon, 200);
    }

    if($user->status == 'rejected'){
        $respon = [
            'status' => 'error',
            'msg' => 'verification error',
            'errors' => 'User account has been rejected. No changes allowed',
            'content' => null,
        ];
        return response()->json($respon, 200);
    }
}

function user_check(User $user){
    if(!$user){
        return response()->json([
            'status' => 'error',
            'msg' => 'User not found',
            'errors' => 'Please provide a valid user',
        ]);
    }
}
