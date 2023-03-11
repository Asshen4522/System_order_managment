<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\activity;
use App\Models\contactPerson;
use App\Models\cost;
use App\Models\locomotive;
use App\Models\order;
use App\Models\report;
use App\Models\reportActivity;
use App\Models\reportCost;
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
        $orders = order::all();
        foreach ($orders as $order) {
            if ($order->created_at <= $request->date  and $order->status_id == 1) {

                $x = order::find($order->id);
                $x->status_id = 2;
                $x->save();
            }
        }
        $answer = DB::table('orders')
            ->leftJoin('users', 'users.id', '=', 'orders.executor_id')
            ->orderBy('orders.id', 'asc')
            ->select('orders.id', 'city', 'status_id', 'users.name', 'users.surname')
            ->get();

        return $answer;
    }

    public function Get_my_orders()
    {
        
        $answer = DB::table('orders')
            ->where(["orders.executor_id" => auth()->user()->id])
            ->where("status_id", '<', 3)
            ->orderBy('orders.id', 'asc')
            ->select('orders.id', 'city', 'status_id')
            ->get();

        return $answer;
    }

    public function Get_report(Request $request)
    {
        $report = report::where([
            "order_id" => $request->id,
            "date" => $request->date
        ])->first();
        $reportActivity = reportActivity::where(["report_id" => $report->id])
            ->get();
        $reportCost = reportCost::where(["report_id" => $report->id])
            ->get();
        $answer = [$report, $reportActivity, $reportCost];
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

    public function Get_display_report(Request $request)
    {
        $report = report::find($request->id);
        $reportActivity = reportActivity::where(["report_id" => $request->id])
            ->get();
        $reportCost = reportCost::where(["report_id" => $request->id])
            ->get();
        $answer = [$report, $reportActivity, $reportCost];
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

    public function Get_inner_costs_report(Request $request)
    {
        $answer = DB::table('reports')
            ->where(["reports.id" => $request->id])
            ->leftJoin('orders', 'reports.order_id', '=', 'orders.id')
            ->select('orders.daily_cost', 'orders.rent')
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

    public function Get_reportList(Request $request)
    {
        $reports = DB::table('reports')
            ->where(['order_id' => $request->id])
            ->select('id', 'date')
            ->get();

        return $reports;
    }

    public function Get_userList()
    {
        $users = user::where("id", '>', 1)
            ->where("id", "!=", auth()->user()->id)
            ->get();
        return $users;
    }

    public function Get_user(Request $request)
    {
        $user = DB::table('users')
            ->where(["users.id" => $request->id])
            ->leftJoin('roles', 'users.role_id', '=', 'roles.id')
            ->select('users.id', 'users.name', 'users.surname', 'users.phone', 'roles.name AS roleName', 'users.role_id')
            ->first();

        return $user;
    }
}
