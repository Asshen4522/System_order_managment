<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class user extends Authenticatable
{
    use HasFactory;
    protected $fillable = [
        'name', 'surname', 'phone', 'role_id', 'password', 'banned'
    ];

    protected $hidden = [
        'password'
    ];

    public $timestamps = FALSE;
}
