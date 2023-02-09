<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\activity;
use App\Models\contactPerson;
use App\Models\cost;
use App\Models\locomotive;
use App\Models\order;
use App\Models\user;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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

    public function Get_orders(Request $request)
    {
        error_log("mes");
        $orders = order::all();
        foreach ($orders as $order) {
            if ($order->created_at <= $request->date  and $order->status_id == 1) {

                $x = order::find($order->id);
                $x->status_id = 2;
                $x->save();
            }
        }
        $answer = order::orderBy('id')
            ->get();

        return $answer;
    }

    public function Get_my_orders()
    {
        $answer = order::where(["executor_id" => auth()->user()->id,])
            ->where("status_id", '<', 3)
            ->get();

        return $answer;
    }

    public function Get_display_order(Request $request)
    {
        $order = DB::table('orders')
            ->leftJoin('contact_people', 'orders.contact_id', '=', 'contact_people.id')
            ->leftJoin('locomotives', 'orders.locomotive_id', '=', 'locomotives.id')
            ->where(["orders.id" => $request->id])
            ->first();
        $order_reports = DB::table('reports')
            ->where(["reports.order_id" => $request->id])
            ->select('id', 'tangen', 'cup', 'wheel_pair')
            ->get();
        $order_costs = DB::table('reports')
            ->leftJoin('report_costs', 'reports.id', '=', 'report_costs.report_id')
            ->where(["reports.order_id" => $request->id])
            ->select('reports.id', 'report_costs.price')
            ->get();
        $answer = [$order, $order_reports, $order_costs];

        return $answer;
    }

    public function Get_order_report_dates(Request $request)
    {
        $answer = DB::table('reports')
            ->where(['order_id' => $request->id])
            ->select(('date'))
            ->get();

        return $answer;
    }

    public function Get_costs()
    {
        $answer = cost::all();

        return $answer;
    }

    public function Get_activities()
    {
        $answer = activity::all();

        return $answer;
    }

    public function Get_costs_order(Request $request)
    {
        $answer = DB::table('orders')
            ->where(["orders.id" => $request->id])
            ->select('daily_cost', 'rent')
            ->get();

        return $answer;
    }

    public function Get_wheel_pair_left(Request $request)
    {
        $order_wheels = DB::table('orders')
            ->where(["orders.id" => $request->id])
            ->select('wheel_pairs', 'status_id')
            ->first();
        $order_reports = DB::table('reports')
            ->where(["reports.order_id" => $request->id])
            ->select('wheel_pair')
            ->get();
        $answer = [$order_wheels, $order_reports];
        return $answer;
    }
}
