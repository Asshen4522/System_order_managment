<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class contactPerson extends Model
{
    use HasFactory;
    protected $fillable = [
        'fio', 'phone'
    ];

    public $timestamps = FALSE;
}
