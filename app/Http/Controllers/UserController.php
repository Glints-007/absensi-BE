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
        if($request->user()->role != 'admin'){
            $respon = [
                'status' => 'error',
                'msg' => 'authorization error',
                'errors' => 'You are not authorized to do this command',
                'content' => null,
            ];
            return response()->json($respon, 403);
        }
        return User::where('role', 'user')->get();
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function indexWithStatus(Request $request, $status)
    {
        if($request->user()->role != 'admin'){
            $respon = [
                'status' => 'error',
                'msg' => 'authorization error',
                'errors' => 'You are not authorized to do this command',
                'content' => null,
            ];
            return response()->json($respon, 403);
        }
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
        if($request->user()->role != 'admin'){
            $respon = [
                'status' => 'error',
                'msg' => 'authorization error',
                'errors' => 'You are not authorized to do this command',
                'content' => null,
            ];
            return response()->json($respon, 403);
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
        if($request->user()->role != 'admin'){
            $respon = [
                'status' => 'error',
                'msg' => 'authorization error',
                'errors' => 'You are not authorized to do this command',
                'content' => null,
            ];
            return response()->json($respon, 403);
        }

        if($user->status == 'verified'){
            $respon = [
                'status' => 'error',
                'msg' => 'verification error',
                'errors' => 'User account has been verified. No changes needed',
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
        if($request->user()->role != 'admin'){
            $respon = [
                'status' => 'error',
                'msg' => 'authorization error',
                'errors' => 'You are not authorized to do this command',
                'content' => null,
            ];
            return response()->json($respon, 403);
        }

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
                'errors' => 'User account has been rejected. No changes needed',
                'content' => null,
            ];
            return response()->json($respon, 200);
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
        if($request->user()->role != 'admin'){
            $respon = [
                'status' => 'error',
                'msg' => 'authorization error',
                'errors' => 'You are not authorized to do this command',
                'content' => null,
            ];
            return response()->json($respon, 403);
        }

        $user->delete();

        return response()->json([
            'status' => 'success',
            'msg' => 'User account has been removed successfully',
            'errors' => null,
        ]);
    }
}
