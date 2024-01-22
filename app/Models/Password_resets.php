<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Password_resets extends Model
{
    use HasFactory;
    protected $dates = ['expires_at'];

    protected $fillable = [
        'email',
        'token',
        'expires_at',
        'is_used',
    ];
}
