<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Booking;
use App\Models\BookingTransaction;
use App\Models\Studio;
use App\Models\User;
use Illuminate\Support\Carbon;
use Stripe\Stripe;
use Stripe\Account;
use Stripe\AccountLink;
use Stripe\Customer;
use Stripe\Token;
use Stripe\Transfer;
use Log;

class TransactionController extends Controller
{
    public function index(Request $request)
    {
        $booking = Booking::select('bookings.*','studios.title as studio_name','users.name as user_name')
        ->leftJoin('studios', 'bookings.studio_id','=','studios.id')
        ->leftJoin('users','bookings.user_id','=','users.id')
        ->where('bookings.booking_date','<=',Carbon::today());
    
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
        return view('admin.transactions.list' , compact(['booking']));
    }

    public function transaction(Request $request)
    {
        $columns = array( 
            0 =>'id',
            1 =>'user_name',
            2 =>'user_email',
            3 =>'studio_name',
            4 => 'booking_date',
            5 => 'amount',
            6 => 'paid_to',
        );  
        $order = $columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');
        $bookingCount = Booking::select('bookings.*','studios.title as studio_name','users.name as user_name','users.email as user_email')
                            ->leftJoin('studios', 'bookings.studio_id','=','studios.id')
                            ->leftJoin('users','bookings.user_id','=','users.id')
                            ->where('bookings.booking_date','<',Carbon::today())->orderBy('bookings.booking_date','desc');
        $booking = Booking::select('bookings.*','studios.title as studio_name','users.name as user_name','users.email as user_email')
                            ->leftJoin('studios', 'bookings.studio_id','=','studios.id')
                            ->leftJoin('users','bookings.user_id','=','users.id')
                            ->where('bookings.booking_date','<',Carbon::today())->orderBy('bookings.booking_date','desc');
        
        if($request->paid_to != null){
            $booking = $booking->where('bookings.paid_to',$request->paid_to);
            $bookingCount = $bookingCount->where('bookings.paid_to',$request->paid_to);
        }

        if($request->search['value'] != null){
            $booking = $booking->where('studios.title','LIKE','%'.$request->search['value'].'%')
                                ->orWhere('bookings.id','LIKE','%'.$request->search['value'].'%')
                                ->orWhere('users.name','LIKE','%'.$request->search['value'].'%')
                                ->orWhere('users.email','LIKE','%'.$request->search['value'].'%')
                                ->orWhere('bookings.booking_date','LIKE','%'.$request->search['value'].'%')
                                ->orWhere('bookings.amount','LIKE','%'.$request->search['value'].'%');

            $bookingCount = $bookingCount->where('studios.title','LIKE','%'.$request->search['value'].'%')
                                    ->orWhere('bookings.id','LIKE','%'.$request->search['value'].'%')
                                    ->orWhere('users.name','LIKE','%'.$request->search['value'].'%')
                                    ->orWhere('users.email','LIKE','%'.$request->search['value'].'%')
                                    ->orWhere('bookings.booking_date','LIKE','%'.$request->search['value'].'%')
                                    ->orWhere('bookings.amount','LIKE','%'.$request->search['value'].'%');
        }
        if($request->length != '-1')
        {
            $booking = $booking->take($request->length);
        }
        $booking = $booking->skip($request->start)
                        ->orderBy($order,$dir)
                        ->get();
        $data = array();
        if(!empty($booking))
        {
            foreach ($booking as $u_booking)
            {
                $url = route('admin.transaction.get', ['id' => $u_booking->id]);
            
                $nestedData['id'] = $u_booking->id;
                $nestedData['user_name'] = $u_booking->user_name;
                $nestedData['user_email'] = $u_booking->user_email;
                $nestedData['studio_name'] = $u_booking->studio_name;
                $nestedData['booking_date'] = $u_booking->booking_date;
                $nestedData['amount'] = '$ '.$u_booking->amount;
                $nestedData['paid_to'] = $u_booking->paid_to == 1 ?  "<span class='badge badge-warning'>Paid</span>" : "<span class='badge badge-danger'>Pay</span>";
                $nestedData['action'] =  "<a href='$url' class='detail-booking btn btn-outline-warning btn-sm btn-icon'><i class='fal fa-eye'></i></a>";
                $data[] = $nestedData;

            }
        }
        return response()->json([
            'draw' => $request->draw,
            'data' =>$data,
            'recordsTotal' => Booking::where('booking_date','<',Carbon::today())->count(),
            'recordsFiltered' => $request->search['value']!= null || $request->paid_to != null ? $bookingCount->count() : Booking::where('booking_date','<',Carbon::today())->count(),
        ]);
    }
    public function getTransaction($id){
        $t_booking = Booking::select('bookings.*','studios.title as studio_name','users.name as user_name','studios.price as price','studios.description as description','studios.rules as rules','studios.cancel_policy as cancel_policy','studios.refund_policy as refund_policy','studios.availability as availability','studios.address as address','studios.city as city','studios.state as state','studios.country as country','studios.area as area')
                            ->leftJoin('studios', 'bookings.studio_id','=','studios.id')
                            ->leftJoin('users','bookings.user_id','=','users.id')
                            ->where('bookings.id',$id)
                            ->first();
        return view('admin.transactions.detail' , compact(['t_booking']));                    
    }

    public function transferStudio(Request $request)
    {
        try{
            $booking = Booking::find($request->booking_id);
            $studio = Studio::find($booking->studio_id);
            $studio_user = User::find($studio->user_id);
            if($booking->paid_to == 1){
                return response()->json(['status' => 'error', 'message' => 'Already Paid to studio!']);
            }
            if($studio_user->stripe_acc_status == 1){
                Stripe::setApiKey(env('STRIPE_SECRATE_KEY'));
                $transfer = Transfer::create([
                    "amount" => $booking->amount * 100,
                    "currency" => "usd",
                    "destination" => $studio_user->stripe_acc_id,
                  ]);
                if(isset($transfer->id)){
                    $booking->paid_to = 1;
                    $booking->save();

                    $trans = new BookingTransaction;
                    $trans->booking_id = $booking->id;
                    $trans->transaction_id	= $transfer->id;
                    $trans->amount = $booking->amount;
                    $trans->status = 1;
                    $trans->type = 1;
                    $trans->save();
                    
                    return response()->json(['status' => 'success', 'message' => 'Paid Successfully!']);
                }
                
                return response()->json(['status' => 'error', 'message' => 'Payment Failed, Please try again!']);    
            }
            return response()->json(['status' => 'error', 'message' => 'Studio stripe account is not activated!']);
        }catch(Exception $e){
            return $this->response([], 'Something went wrong please try again.', false,404);
        }
        
        return response()->json($request->booking_id);
    }
}   
