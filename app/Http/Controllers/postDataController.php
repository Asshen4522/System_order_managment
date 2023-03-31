<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\reportWheel;
use App\Models\locomotive;
use App\Models\activity;
use App\Models\contactPerson;
use App\Models\cost;
use App\Models\order;
use App\Models\report;
use App\Models\reportActivity;
use App\Models\reportCost;
use App\Models\orderLocomotive;
use App\Models\user;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

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
    public function Create_model(Request $request)
    {
        try {
            $train = locomotive::create([
                'model' => $request->name,
                'wheel_pairs' => $request->wheel_pairs
            ]);
            $train->refresh();
            return $train->id;
        } catch (\Throwable $th) {
            $answer="false";
            return $th;
        }
        

        return ($person->id);
    }

    
    public function Create_order(Request $request)
    {
        try {
            $order = order::create([
                'name' => $request->name,

                'firm' => $request->firm,
                'city' => $request->city,
                'housing' => $request->housing,

                'budget' => $request->budget,
                'daily_cost' => $request->daily_cost,
                'rent' => $request->rent,
                'payment' => $request->payment,

                'tangen' => $request->tangen,
                'cup' => $request->cup,
                
                'contact_id' => $request->contact,
                'created_at' => $request->created_at,
                'order_created_at' => $request->order_created_at,
                'executor_id' => $request->executor,
                'status_id' => 1
            ]);
            $order->refresh();
            foreach ($request->locomotive as $locomotive) {
                orderLocomotive::create([
                    'order_id' => $order->id,
                    'locomotive_id' => $locomotive['id'],
                    'amount' => (int)$locomotive['count'],
                    'wheel_pairs' => (int)$locomotive['wheel_pairs'],
                ]);
            }
            return true;
        } catch (\Throwable $th) {
            return $th;
        }
    }

    public function Delete_order(Request $request)
    {
        try {
            order::destroy($request->id);
            $answer = "true";
            return $answer;
        } catch (\Throwable $th) {
            $answer = "false";
            return $answer;
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
                'comment' => $request->comment,
            ]);
            $report->refresh();
            if (count($request->wheel_pairs)) {
                foreach ($request->wheel_pairs as $train) {
                    reportWheel::create([
                        'report_id' => $report->id,
                        'locomotive_id' => $train['locomotive_id'],
                        'amount' => (float)$train['doneNow'],
                    ]);
                    $pack = orderLocomotive::find($train['id']);
                    $pack->done = (float)$train['doneNow'] + (float)$train['done'];
                    $pack->save();
                }
            }
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
            return $th;
        }
    }
    public function Edit_report(Request $request)
    {
        try {
            $report = report::find($request->id);
            $report->tangen = (int)$request->tangen;
            $report->cup = (int)$request->cup;
            $report->comment = $request->comment;
            $report->save();

            reportCost::where('report_id', '=', $request->id)->delete();
            reportActivity::where('report_id', '=', $request->id)->delete();
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

            if (count($request->wheel_pairs)) {
                foreach ($request->wheel_pairs as $train) {
                    $base_train = reportWheel::find($train['train_id']);
                    $base_train->amount = (float)$train['doneNow'];
                    $base_train->save();

                    $pack = orderLocomotive::find($train['id']);
                    $pack->done = (float)$train['doneNow'] + (float)$train['done'];
                    $pack->save();
                }
            }

            $answer = "true";
            return $answer;
        } catch (\Throwable $th) {
            return $th;
        }
    }


    public function Edit_order(Request $request)
    {
        try {
            $order = order::find($request->id);
            $order->name = $request->name;
            $order->firm = $request->firm;
            $order->city = $request->city;

            $order->budget = $request->budget;
            $order->daily_cost = $request->daily_cost;
            $order->housing = $request->housing;
            $order->rent = $request->rent;
            $order->payment = $request->payment;

            $order->tangen = $request->tangen;
            $order->cup = $request->cup;

            $order->contact_id = $request->contact;
            $order->created_at = $request->created_at;
            $order->order_created_at = $request->order_created_at;
            $order->executor_id = $request->executor;
            $order->save();

            orderLocomotive::where('order_id', '=', $request->id)->delete();

            foreach ($request->locomotive as $locomotive) {
                orderLocomotive::create([
                    'order_id' => $order->id,
                    'locomotive_id' => $locomotive['id'],
                    'amount' => (int)$locomotive['amount'],
                    'wheel_pairs' => (int)$locomotive['wheel_pairs'],
                ]);
            }

            $answer = "true";
            return $answer;
        } catch (\Throwable $th) {
            return $th;
        }
    }

    public function Create_cost(Request $request)
    {
        try {
            $cost = cost::create([
                'name' => $request->name
            ]);
            $cost->refresh();
            return $cost->id;
        } catch (\Throwable $th) {
            $answer = "false";
            return $answer;
        }
    }

    public function Create_activity(Request $request)
    {
        try {
            $activity = activity::create([
                'name' => $request->name
            ]);
            $activity->refresh();
            return $activity->id;
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
            if ($request->status == 3) {
                $order->date_end = $request->date;
            }
            $order->save();
            $answer = "true";
            return $answer;
        } catch (\Throwable $th) {
            dd($th);
        }
    }
    public function Delete_user(Request $request)
    {
        try {
            user::destroy($request->id);
            $answer = "true";
            return $answer;
        } catch (\Throwable $th) {
            $answer = "false";
            return $answer;
        }
    }
    public function Edit_user(Request $request)
    {
        try {
            $user = user::find($request->id);
            $user->phone = $request->phone;
            $user->name = $request->name;
            $user->surname = $request->surname;
            $user->role_id = $request->roleId;
            if ($request->password) {
                $user->password = Hash::make($request->password);
            }
            $user->save();

            $answer = "true";
            return $answer;
        } catch (\Throwable $th) {
            return $th;
        }
    }
}
