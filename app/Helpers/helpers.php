<?php

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Office;

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

function check_distance(Request $request){
    $office = Office::find($request->user()->office_id);
    $response = \GoogleMaps::load('distancematrix')
            ->setParam([
                'destinations'     => $office->lat.', '.$office->long,
                'origins'     => $request->lat.', '.$request->long,
            ])->get('rows');

    if($response['rows'][0]['elements'][0]['distance']['value'] <= 100){
        return true;
    }
    else{
        return false;
    }
}
