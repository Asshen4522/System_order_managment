<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\contactPerson;
use App\Models\order;
use Illuminate\Http\Request;

class postDataController extends Controller
{
    public function Create_contact(Request $request)
    {

        $person = contactPerson::create([
            'fio' => $request->fio,
            'phone' => $request->phone
        ]);

        return ($person->id);
    }
    public function Create_order(Request $request)
    {
        try {
            $request = order::create([
                'city' => $request->city,
                'locomotive_id' => $request->locomotive,
                'budget' => $request->budget,
                'housing' => $request->housing,
                'tangen' => $request->tangen,
                'cup' => $request->cup,
                'wheel_pairs' => $request->wheel_pairs,
                'contact_id' => $request->contact,
                'created_at' => $request->created_at,
                'executor_id' => $request->executor,
                'status_id' => 1
            ]);

            return true;
        } catch (\Throwable $th) {
            return false;
        }
    }
}
