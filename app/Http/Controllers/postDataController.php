<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\activity;
use App\Models\contactPerson;
use App\Models\cost;
use App\Models\order;
use App\Models\report;
use App\Models\reportActivity;
use App\Models\reportCost;
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
                'daily_cost' => $request->daily_cost,
                'housing' => $request->housing,
                'rent' => $request->rent,
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
    public function Create_report(Request $request)
    {
        try {
            $report = report::create([
                'date' => $request->date,
                'order_id' => $request->id,
                'tangen' => (int)$request->tangen,
                'cup' => (int)$request->cup,
                'wheel_pair' => (float)$request->wheel_pairs,
                'comment' => $request->comment,
            ]);
            $report->refresh();
            if (count($request->costs)) {
                foreach ($request->costs as $cost) {
                    reportCost::create([
                        'report_id' => $report->id,
                        'cost_id' => $cost['id'],
                        'price' => (int)$cost['price'],
                    ]);
                }
            }
            if (count($request->activities)) {
                foreach ($request->activities as $activity) {
                    reportActivity::create([
                        'report_id' => $report->id,
                        'activity_id' => $activity['id'],
                    ]);
                }
            }

            $answer = 'true';
            return $answer;
        } catch (\Throwable $th) {
            $answer = 'false';
            return $th;
        }
    }

    public function Edit_order(Request $request)
    {
        try {
            $order = order::find($request->id);
            $order->city = $request->city;
            $order->locomotive_id = $request->locomotive;
            $order->budget = $request->budget;
            $order->daily_cost = $request->daily_cost;
            $order->housing = $request->housing;
            $order->rent = $request->rent;
            $order->tangen = $request->tangen;
            $order->cup = $request->cup;
            $order->wheel_pairs = $request->wheel_pairs;
            $order->contact_id = $request->contact;
            $order->created_at = $request->created_at;
            $order->executor_id = $request->executor;
            $order->save();

            $answer = "true";
            return $answer;
        } catch (\Throwable $th) {
            return $th;
        }
    }

    public function Create_cost(Request $request)
    {
        try {
            cost::create([
                'name' => $request->name
            ]);
            $answer = "true";
            return $answer;
        } catch (\Throwable $th) {
            $answer = "false";
            return $answer;
        }
    }

    public function Create_activity(Request $request)
    {
        try {
            activity::create([
                'name' => $request->name
            ]);
            $answer = "true";
            return $answer;
        } catch (\Throwable $th) {
            $answer = "false";
            return $answer;
        }
    }

    public function Update_status(Request $request)
    {
        try {
            $order = order::find($request->id);
            $order->status_id = $request->status;
            $order->save();
            $answer = "true";
            return $answer;
        } catch (\Throwable $th) {
            dd($th);
        }
    }
}
