<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class report extends Model
{
    use HasFactory;
    protected $fillable = [
        'date', 'order_id', 'tangen', 'cup', 'comment', 'wheel_pair'
    ];

    public $timestamps = FALSE;
}
