<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Booking;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Session;
use Hash;
use Mail;

class BookingController extends Controller
{
    public function index(Request $request)
    {
        $booking = Booking::select('bookings.*','studios.title as studio_name','users.name as user_name')
        ->leftJoin('studios', 'bookings.studio_id','=','studios.id')
        ->leftJoin('users','bookings.user_id','=','users.id');
    
        if($request->search != null)
        {
            $booking = $booking->where('booking_date','LIKE','%'.$request->search.'%');
                
        }
        
        if($request->sortby!= null && $request->sorttype)
        {
            $booking = $booking->orderBy($request->sortby,$request->sorttype);
        }else{
            $booking = $booking->orderBy('id','desc');
        }
        if($request->perPage != null){
            $booking = $booking->paginate($request->perPage);
        }else{
            $booking = $booking->paginate(10);
        }
        if($request->ajax())
        {
            return response()->json( view('admin.booking.booking_data', compact('u_videos'))->render());
        }
        return view('admin.booking.list' , compact(['booking']));
    }

    public function bookings(Request $request)
    {
        $columns = array( 
            0 =>'id',
            1 =>'user_name',
            2 =>'user_email',
            3 =>'studio_name',
            4 => 'booking_date',
            5 => 'amount',
        );  
        $order = $columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');

        $booking = Booking::select('bookings.*','studios.title as studio_name','users.name as user_name','users.email as user_email')
                            ->leftJoin('studios', 'bookings.studio_id','=','studios.id')
                            ->leftJoin('users','bookings.user_id','=','users.id');
                          
        
        if($request->type != null){
            $booking = $booking->where('type',$request->type);
            
        }

        if($request->search['value'] != null){
            $booking = $booking->where('studios.title','LIKE','%'.$request->search['value'].'%')
                                ->orWhere('users.name','LIKE','%'.$request->search['value'].'%')
                                ->orWhere('users.email','LIKE','%'.$request->search['value'].'%')
                                ->orWhere('bookings.booking_date','LIKE','%'.$request->search['value'].'%')
                                ->orWhere('bookings.amount','LIKE','%'.$request->search['value'].'%')
                                ->orWhere('bookings.id','LIKE','%'.$request->search['value'].'%');
        }
        if($request->length != '-1')
        {
            $booking = $booking->take($request->length);
        }else{
            $booking = $booking->take(Booking::count());
        }
        $booking = $booking->skip($request->start)
                        ->orderBy($order,$dir)
                        ->get();
        $data = array();
        if(!empty($booking))
        {
            foreach ($booking as $u_booking)
            {
                $url = route('admin.booking.get', ['user_id' => $u_booking->id]);
            
                $nestedData['id'] = $u_booking->id;
                $nestedData['user_name'] = $u_booking->user_name;
                $nestedData['user_email'] = $u_booking->user_email;
                $nestedData['studio_name'] = $u_booking->studio_name;
                $nestedData['booking_date'] = $u_booking->booking_date;
                $nestedData['amount'] = "<span>$</span> $u_booking->amount"; 
                $nestedData['action'] =  "<button class='detail-booking btn btn-outline-warning btn-sm btn-icon' data-url=' $url '><i class='fal fa-eye'></i></button>";
                $data[] = $nestedData;

            }
        }
        return response()->json([
            'draw' => $request->draw,
            'data' =>$data,
            'recordsTotal' => Booking::all()->count(),
           'recordsFiltered' => $request->search['value']!= null ? $u_booking = Booking::all()->count() : $u_booking = Booking::all()->count(),
        ]);
    }
    public function getBooking(Request $request){
        $u_booking = Booking::select('bookings.*','studios.title as studio_name','users.name as user_name')
                            ->leftJoin('studios', 'bookings.studio_id','=','studios.id')
                            ->leftJoin('users','bookings.user_id','=','users.id')
                            ->where('bookings.id',$request->user_id)
                            ->first();
        return response()->json(['data'=>$u_booking]);
    }

}
