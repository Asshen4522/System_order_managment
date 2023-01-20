<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class order extends Model
{
    use HasFactory;

    protected $fillable = [
        'city', 'locomotive_id', 'budget', 'housing', 'tangen', 'cup', 'wheel_pairs', 'contact_id', 'created_at', 'date_end', 'executor_id', 'status_id'
    ];

    public $timestamps = FALSE;
}
