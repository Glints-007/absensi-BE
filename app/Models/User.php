<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $primaryKey = 'uid';
    public $incrementing = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'uid',
        'name',
        'email',
        'password',
        'office_id',
        'status',
        'role',
        'absent',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function office()
    {
        return $this->belongsTo(Office::class, 'office_id');
    }

    public function clock()
    {
        return $this->hasMany(Clock::class,'user_id' ,'uid');
    }

    public static function absent()
    {
        return self::where('role', 'user')->where('absent', '>=', 3)->get();
    }

    public static function boot() {
        parent::boot();
        self::deleting(function($user) {
             $user->clock()->each(function($clock) {
                $clock->delete();
             });
        });
    }
}
