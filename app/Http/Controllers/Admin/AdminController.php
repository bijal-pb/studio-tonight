<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Video;
use App\Models\FoodCategory;
use App\Models\Studio;
use App\Models\Booking;
use App\Models\Coupon;

class AdminController extends Controller
{
    public function index()
    {
        
        $data = (object) [];
        $data->total_users = User::whereHas('roles', function($q){
            $q->whereIn('name', ['user']);
        })->count();
        $data->total_studios = Studio::count();
        $data->total_bookings = Booking::count();
        $data->total_coupons = Coupon::count();

        $chart_data = User::select(\DB::raw("COUNT(*) as count"), \DB::raw("(DATE_FORMAT(created_at, '%d-%m-%Y')) as udate"))
                        ->groupBy('udate')
                        ->get();
        $cData = [];

        foreach($chart_data as $row) {
            $timestamp = null;
            // $date = "1-".$row->monthyear;
            $timestamp = strtotime(date($row->udate)) * 1000; 
            array_push($cData,[$timestamp, (int) $row->count]);
        }

        $data->chart_data = json_encode($cData);
        return view('admin.home')->with("data", $data);
    }
}
