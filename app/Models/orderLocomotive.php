<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class orderLocomotive extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'locomotive_id', 'order_id','amount','wheel_pairs','done'
    ];

    public $timestamps = FALSE;
}
