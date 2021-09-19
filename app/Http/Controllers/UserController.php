<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        return User::where('role', 'user')->get();
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function indexWithStatus(Request $request, $status)
    {
        return User::where([
            ['role', 'user'],
            ['status', $status],
        ])->get();
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, User $user)
    {
        $check = user_check($user);
        if($check){
            return $check;
        }
        return $user;
    }

    /**
     * Approve user registration account.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function verify(Request $request, User $user)
    {
        $check = user_check($user);
        $status = status_check($user);
        if($check || $status){
            return $check ?? $status;
        }

        $user->status = 'verified';
        $user->save();

        return response()->json([
            'status' => 'success',
            'msg' => 'User account has been verified successfully',
            'errors' => null,
        ]);
    }

    /**
     * Reject user registration account.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function reject(Request $request, User $user)
    {
        $check = user_check($user);
        $status = status_check($user);
        if($check || $status){
            return $check ?? $status;
        }

        $user->status = 'rejected';
        $user->save();

        return response()->json([
            'status' => 'success',
            'msg' => 'User account has been rejected successfully',
            'errors' => null,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, User $user)
    {
        $check = user_check($user);
        if($check){
            return $check;
        }

        $user->delete();

        return response()->json([
            'status' => 'success',
            'msg' => 'User account has been removed successfully',
            'errors' => null,
        ]);
    }
}
