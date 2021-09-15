<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Office extends Model
{
    use HasFactory;

    protected $table = 'offices';
    protected $fillable = [
        'name',
        'lat',
        'long',
    ];

    public function user()
    {
        return $this->hasMany(User::class, 'office_id');
    }
}
