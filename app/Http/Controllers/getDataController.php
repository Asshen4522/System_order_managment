<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\orderLocomotive;
use App\Models\reportWheel;
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
            ->select('orders.id','orders.name as orderId', 'city', 'status_id', 'users.name', 'users.surname','orders.created_at')
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
        $reportWheel = reportWheel::where(["report_id" => $report->id])
            ->get();
        $answer = [$report, $reportActivity, $reportCost,$reportWheel];
        return $answer;
    }

    public function Get_display_order(Request $request)
    {
        $order = DB::table('orders')
            ->where(["orders.id" => $request->id])
            ->leftJoin('contact_people', 'orders.contact_id', '=', 'contact_people.id')
            ->first();
        $order_reports = DB::table('reports')
            ->where(["reports.order_id" => $request->id])
            ->select('id', 'tangen', 'cup')
            ->get();
        $order_costs = DB::table('reports')
            ->leftJoin('report_costs', 'reports.id', '=', 'report_costs.report_id')
            ->where(["reports.order_id" => $request->id])
            ->select('reports.id', 'report_costs.price')
            ->get();
        $order_locomotives = DB::table('order_locomotives')
            ->where(["order_locomotives.order_id" => $request->id])
            ->leftJoin('locomotives', 'locomotives.id', '=', 'order_locomotives.locomotive_id')
            ->select('locomotives.id','locomotives.model','order_locomotives.amount','order_locomotives.wheel_pairs','order_locomotives.done')
            ->get();
        $answer = [$order, $order_reports, $order_costs,$order_locomotives];

        return $answer;
    }

    public function Get_display_report(Request $request)
    {
        $report = report::find($request->id);
        $reportActivity = reportActivity::where(["report_id" => $request->id])
            ->get();
        $reportCost = reportCost::where(["report_id" => $request->id])
            ->get();
        $reportWheels = reportWheel::where(["report_id" => $request->id])
            ->leftJoin('locomotives', 'locomotives.id', '=', 'locomotive_id')
            ->get();
        $answer = [$report, $reportActivity, $reportCost, $reportWheels];
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
        $orders = DB::table('orders')
            ->where(["orders.id" => $request->id])
            ->select('daily_cost', 'rent')
            ->get();

        $cutters = DB::table('orders')
            ->where(["orders.id" => $request->id])
            ->select('tangen', 'cup')
            ->get();
        $cuttersDone = DB::table('reports')
            ->where(["reports.order_id" => $request->id])
            ->select('tangen', 'cup','date')
            ->get();

        $answer=[$orders,$cutters,$cuttersDone];
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

    public function Get_wheel_pair(Request $request)
    {
        $order_locomotives = DB::table('order_locomotives')
        ->where(["order_locomotives.order_id" => $request->id])
        ->leftJoin('locomotives', 'locomotives.id', '=', 'order_locomotives.locomotive_id')
        ->select('order_locomotives.*', 'locomotives.model')
        ->get();
        
        return $order_locomotives;
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
    
    public function Get_stats()
    {
        $orders = order::all();
        $reports = report::all();
        $report_wheels = DB::table('report_wheels')
            ->leftJoin('reports', 'reports.id', '=', 'report_wheels.report_id')
            ->select('reports.date', 'report_wheels.*')
            ->get();
        
        $order_locomotives = DB::table('order_locomotives')
            ->leftJoin('orders', 'orders.id', '=', 'order_locomotives.order_id')
            ->select('orders.created_at', 'order_locomotives.*')
            ->get();
        $answer = [$orders, $reports, $order_locomotives, $report_wheels];
        return $answer ;
    }
}
