<?php

namespace App\Http\Controllers;

use App\Models\Clock;
use App\Models\Office;
use Illuminate\Http\Request;

class ClockController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        return Clock::where('user_id', $request->user()->uid)->latest()->get();
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if(check_distance($request)){
            $clock = Clock::TodayClock();
            if($clock){
                return response()->json([
                    'status' => 'error',
                    'msg' => 'You\'ve already clocked in at '.$clock->clock_in,
                    'errors' => null,
                ]);
            }
            Clock::create([
                'user_id' => $request->user()->uid,
                'clock_in' => now(),
                'clock_in_lat' => $request->lat,
                'clock_in_long' => $request->long,
            ]);

            return response()->json([
                'status' => 'success',
                'msg' => 'You\'ve been clocked in successfully',
                'errors' => null,
            ]);
        }
        else{
            return response()->json([
                'status' => 'failed',
                'msg' => 'Clock in denied. You must be at least 100 meters away from the office',
                'errors' => null,
            ]);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Clock  $clock
     * @return \Illuminate\Http\Response
     */
    public function show(Clock $clock)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Clock  $clock
     * @return \Illuminate\Http\Response
     */
    public function edit(Clock $clock)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $clock = Clock::TodayClock();
        if($clock){
            if(check_distance($request)){
                if($clock->clock_out){
                    return response()->json([
                        'status' => 'error',
                        'msg' => 'You\'ve already clocked out at '.$clock->clock_out,
                        'errors' => null,
                    ]);
                }
                $clock->clock_out = now();
                $clock->clock_out_lat = $request->lat;
                $clock->clock_out_long = $request->long;
                $clock->total_working_hours = strtotime($clock->clock_out) - strtotime($clock->clock_in);
                $clock->save();

                return response()->json([
                    'status' => 'success',
                    'msg' => 'You\'ve been clocked out successfully',
                    'errors' => null,
                ]);
            }
            else{
                return response()->json([
                    'status' => 'failed',
                    'msg' => 'Clock out denied. You must be at least 100 meters away from the office',
                    'errors' => null,
                ]);
            }
        }
        else{
            return response()->json([
                'status' => 'error',
                'msg' => 'Clock out failed. You haven\'t clocked in for today',
                'errors' => null,
            ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Clock  $clock
     * @return \Illuminate\Http\Response
     */
    public function destroy(Clock $clock)
    {
        //
    }
}
