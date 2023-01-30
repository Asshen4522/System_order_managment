<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\contactPerson;
use App\Models\locomotive;
use App\Models\order;
use App\Models\report;
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

    public function Get_orders()
    {
        $answer = order::all();

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
}
