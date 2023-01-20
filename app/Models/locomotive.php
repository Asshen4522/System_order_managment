<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class locomotive extends Model
{
    use HasFactory;

    protected $fillable = [
        'model', 'wheel_pairs'
    ];

    public $timestamps = FALSE;
}
