<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class reportCost extends Model
{
    use HasFactory;

    protected $fillable = [
        'report_id', 'cost_id', 'price'
    ];

    public $timestamps = FALSE;
}
