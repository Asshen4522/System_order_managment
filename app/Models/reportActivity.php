<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class reportActivity extends Model
{
    use HasFactory;
    protected $fillable = [
        'report_id', 'activities_id'
    ];

    public $timestamps = FALSE;
}
