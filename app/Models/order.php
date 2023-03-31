<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class order extends Model
{
    use HasFactory;

    protected $fillable = [
        'city', 'budget','firm', 'daily_cost', 'housing', 'rent', 'payment', 'tangen', 'cup', 'contact_id', 'created_at', 'date_end', 'executor_id', 'status_id', 'order_created_at','name'
    ];

    public $timestamps = FALSE;
}


