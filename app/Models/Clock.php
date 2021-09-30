<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Clock extends Model
{
    use HasFactory;

    protected $table = 'clocks';
    protected $fillable = [
        'user_id',
        'clock_in',
        'clock_out',
        'clock_in_lat',
        'clock_in_long',
        'clock_out_lat',
        'clock_out_long',
        'total_working_hours',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public static function TodayClock($uid)
    {
        return self::where('uid', $uid)->whereDate('created_at', now())->first();
    }
}
