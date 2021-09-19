<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class Admin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
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

        return $next($request);
    }
}
