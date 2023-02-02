<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\contactPerson;
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
                'tangen' => $request->tangen,
                'cup' => $request->cup,
                'wheel_pair' => $request->wheel_pairs,
                'comment' => $request->comment,
            ]);
            $report->refresh();
            if (count($request->costs)) {
                foreach ($request->costs as $cost) {
                    reportCost::create([
                        'report_id' => $report->id,
                        'cost_id' => $cost['id'],
                        'price' => $cost['price'],
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
}
