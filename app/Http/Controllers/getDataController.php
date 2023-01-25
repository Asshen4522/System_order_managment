<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\contactPerson;
use App\Models\locomotive;
use App\Models\order;
use App\Models\user;
use Illuminate\Http\Request;

class getDataController extends Controller
{
    public function Get_locomotives()
    {
        $answer = locomotive::all();

        return $answer;
    }

    public function Get_contacts()
    {
        $answer = contactPerson::all();

        return $answer;
    }

    public function Get_executors()
    {
        $answer = user::where(["role_id" => 2])->get();

        return $answer;
    }

    public function Get_orders()
    {
        $answer = order::all();

        return $answer;
    }
}
