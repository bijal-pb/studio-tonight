<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Traits\ApiTrait;
use App\Models\Booking;
use App\Models\User;
use App\Models\BookingTransaction;
use App\Models\BookingService;
use App\Models\Studio;
use App\Models\StudioService;
use App\Models\StudioTiming;
use Illuminate\Support\Facades\Validator;
use Exception;
use DB;
use App\Models\Coupon;
use Auth;
use Illuminate\Support\Carbon;
use Carbon\CarbonPeriod;
use Stripe\Checkout\Session;
use Stripe\Stripe;
use Stripe\StripeClient;
use Mail;


class BookingController extends Controller
{
    use ApiTrait;

    /**
     *  @OA\Get(
     *     path="/api/studio/calender",
     *     tags={"Studio"},
     *     security={{"bearer_token":{}}},  
     *     summary=" Studio Calender",
     *     operationId="studio-calender",
     * 
     * 
     *     @OA\Parameter(
     *         name="booking_date",
     *         in="query",
     *         description="",
     *         required= true,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Success",
     *         @OA\MediaType(
     *             mediaType="application/json",
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Invalid request"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="not found"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Unprocessable entity"
     *     ),
     * )
    **/
    public function studioCalender(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'booking_date' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->response([], $validator->errors()->first(), false, 404);
        }
        $month = Carbon::parse($request->booking_date)->format('m');
        $year = Carbon::parse($request->booking_date)->format('Y');
        $studios = Studio::where('user_id',Auth::id())->pluck('id')->toArray();
        try{
            $bookings = Booking::with(['book_by','studio'])->whereIn('studio_id',$studios)->whereMonth('booking_date', $month)
                ->whereYear('booking_date', $year)
                ->get();
            $dateBookings = Booking::with(['book_by','studio'])->whereIn('studio_id',$studios)->whereDate('booking_date',$request->booking_date)->get();
            return $this->response([
                "month" => $bookings,
                "date" => $dateBookings
            ]);
        }catch(Exception $e){
            return $this->response([], $e->getMessage(), false, 400);
        }
    }
    /**
     *  @OA\Get(
     *     path="/api/studio/booking/all",
     *     tags={"Studio"},
     *     security={{"bearer_token":{}}},  
     *     summary="All Studio Booking ",
     *     operationId="studio-bookingall",
     * 
     * 
     *     @OA\Parameter(
     *         name="studio_id",
     *         in="query",
     *         description="",
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="from_date",
     *         in="query",
     *         description="",
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *      @OA\Parameter(
     *         name="to_date",
     *         in="query",
     *         description="",
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *      @OA\Parameter(
     *         name="start_time",
     *         in="query",
     *         description="",
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *      @OA\Parameter(
     *         name="end_time",
     *         in="query",
     *         description="",
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Success",
     *         @OA\MediaType(
     *             mediaType="application/json",
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Invalid request"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="not found"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Unprocessable entity"
     *     ),
     * )
    **/

    public function bookings(Request $request)
    {

        try{
            $studios = Studio::where('user_id',Auth::id())->pluck('id')->toArray();
            $bookings = Booking::with(['book_by','studio']);
            if(isset($request->studio_id) && ($request->studio_id != null || $request->studio_id != 'null')) {
                $bookings = $bookings->where('studio_id',$request->studio_id);
            }else{
                $bookings = $bookings->whereIn('studio_id',$studios);
            }
            if(isset($request->from_date) && $request->from_date != null && isset($request->to_date) && $request->to_date != null ) {
                $bookings = $bookings->whereBetween('booking_date',[$request->from_date, $request->to_date]);
            }
            if(isset($request->start_time) && $request->start_time != null && isset($request->end_time) && $request->end_time != null ) {
                $bookings = $bookings->where('start_time','>=',$request->start_time);
                $bookings = $bookings->where('end_time','<=',$request->end_time);
            }
            $bookings = $bookings->orderBy('booking_date','desc')->paginate(100);
            return $this->response($bookings);
        }catch(Exception $e){
            return $this->response([], $e->getMessage(), false, 400);
        } 
    }

    /**
     *  @OA\Post(
     *     path="/api/studio/booking/detail",
     *     tags={"Studio"},
     *     security={{"bearer_token":{}}},  
     *     summary=" All Studio  Details",
     *     operationId="studio-booking",
     * 
     * 
     *     @OA\Parameter(
     *         name="booking_id",
     *         in="query",
     *         description="",
     *         required= true,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Success",
     *         @OA\MediaType(
     *             mediaType="application/json",
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Invalid request"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="not found"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Unprocessable entity"
     *     ),
     * )
    **/

    public function bookingDetail(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'booking_id' => 'required|exists:bookings,id',
        ]);

        if ($validator->fails()) {
            return $this->response([], $validator->errors()->first(), false, 404);
        }
        try{
            $booking = Booking::with(['book_by','studio','studio_services','reviews','feedbacks'])->where('id',$request->booking_id)->first();
            return $this->response($booking);
        }catch(Exception $e){
            return $this->response([], $e->getMessage(), false, 400);
        } 
    }

    /**
     *  @OA\Get(
     *     path="/api/customer/booking/all",
     *     tags={"Customer"},
     *     security={{"bearer_token":{}}},  
     *     summary="customer all Bookings ",
     *     operationId="customer-boolkings",
     * 
     * 
     *     @OA\Response(
     *         response=200,
     *         description="Success",
     *         @OA\MediaType(
     *             mediaType="application/json",
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Invalid request"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="not found"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Unprocessable entity"
     *     ),
     * )
    **/

    public function customerBookings(Request $request)
    {
        try{
            $booking = Booking::with(['book_by','studio'])->where('user_id',Auth::id())->orderBy('id','desc')->get();
            return $this->response($booking);
        }catch(Exception $e){
            return $this->response([], $e->getMessage(), false, 400);
        } 
    }

    /**
     *  @OA\Get(
     *     path="/api/valid/coupon",
     *     tags={"Booking"},
     *     security={{"bearer_token":{}}},  
     *     summary="Check valid coupon",
     *     operationId="valid-coupon", 
     *     
     *     @OA\Parameter(
     *         name="code",
     *         in="query",
     *         description="",
     *         required= true,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     * 
     *     @OA\Response(
     *         response=200,
     *         description="Success",
     *         @OA\MediaType(
     *             mediaType="application/json",
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Invalid request"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="not found"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Unprocessable entity"
     *     ),
     * )
    **/

    public function CouponCheck(Request $request)
    {   
        try{
            $coupon = Coupon::where('code',$request->code)->first();
            if($coupon){
                if($coupon->apply_users < $coupon->total_users && $coupon->expiry_date >= Carbon::now()->format('Y-m-d')){
                    return $this->response($coupon, 'coupon code is valid!');
                }
                return $this->response([], 'Coupon code is expire!', false, 400);
            }
            return $this->response([], 'Enter valid coupon code!', false, 400);
        } catch (Exception $e){
            return $this->response([], $e->getMessage(), false, 400);
        } 
    }

    /**
     *  @OA\Get(
     *     path="/api/studio/slots",
     *     tags={"Customer"},
     *     security={{"bearer_token":{}}},  
     *     summary="Check Available slots of studio ",
     *     operationId="studio-slots",
     * 
     * 
     *     @OA\Parameter(
     *         name="studio_id",
     *         in="query",
     *         description="",
     *         required= true,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="booking_date",
     *         in="query",
     *         description="",
     *         required= true,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="book_hour",
     *         in="query",
     *         description="",
     *         required= true,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     * 
     *     @OA\Response(
     *         response=200,
     *         description="Success",
     *         @OA\MediaType(
     *             mediaType="application/json",
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Invalid request"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="not found"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Unprocessable entity"
     *     ),
     * )
    **/
    public function availableSlots(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'studio_id' => 'required|exists:studios,id',
            'booking_date' => 'required',
            'book_hour' => 'required'
        ]);

        if ($validator->fails()) {
            return $this->response([], $validator->errors()->first(), false, 404);
        }

        try{
            $day = strtolower(Carbon::parse($request->booking_date)->format('D'));
            $studio = StudioTiming::where('studio_id',$request->studio_id)->first();
            if($studio->$day == 1){
                $interval = $request->book_hour * 60;
                $startKey = $day.'_start_time';
                $start_time = substr($studio->$startKey,0,-3);
                $endKey = $day.'_end_time';
                $end_time = substr($studio->$endKey,0,-3);
                $slots = $this->getTimeSlot($interval,$start_time,$end_time);
                $bookings = Booking::where('studio_id',$request->studio_id)->whereDate('booking_date',$request->booking_date)->get();
                if(isset($bookings) && count($bookings) > 0)
                {
                    foreach($bookings as $booking){
                        $bookingStart = strtotime(carbon::parse($booking->start_time)->format('H:i'));
                        $bookingEnd = strtotime(carbon::parse($booking->end_time)->format('H:i'));
                        foreach($slots as &$slot){
                            $slotStart = strtotime($slot['start_time']);
                            $slotEnd = strtotime($slot['end_time']);
                            if($bookingStart <= $slotEnd && $bookingEnd >= $slotStart){
                                    $slot['is_booked'] = 1;
                            }
                        }
                    }
                }
                
                return $this->response($slots);
            }
            return $this->response([], 'Studio Closed', false, 400);
        }catch(Exception $e){
            return $this->response([], $e->getMessage(), false, 400);
        } 
    }


    public function getTimeSlot($interval, $start_time, $end_time)
    {
        $period = new CarbonPeriod($start_time, $interval.' minutes', $end_time); // for create use 24 hours format later change format 
        $slots = [];
        foreach($period as $item){
            if($start_time == $item->format("H:i")){
                continue;
            }
            $s = ["start_time" => carbon::parse($start_time)->addMinute()->format("H:i"), "end_time"=>$item->format("H:i"), "is_booked"=> 0];
            array_push($slots,$s);
            $start_time = $item->format("H:i");
        }
        return $slots;
    }

    /**
     *  @OA\Post(
     *     path="/api/customer/studio/book",
     *     tags={"Customer"},
     *     security={{"bearer_token":{}}},  
     *     summary="Book Studio",
     *     operationId="customer-studio-book",
     * 
     * 
     *     @OA\Parameter(
     *         name="studio_id",
     *         in="query",
     *         description="",
     *         required= true,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *    @OA\Parameter(
     *         name="date",
     *         in="query",
     *         description="YYYY-MM-DD",
     *         required= true,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="start_time",
     *         in="query",
     *         description="hh:mm",
     *         required= true,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="end_time",
     *         in="query",
     *         description="hh:mm",
     *         required= true,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *      @OA\Parameter(
     *         name="total_hour",
     *         in="query",
     *         description="",
     *         required= true,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *      @OA\Parameter(
     *         name="discount",
     *         in="query",
     *         description="",
     *         required= true,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *         @OA\Parameter(
     *         name="fees",
     *         in="query",
     *         description="",
     *         required= true,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *      @OA\Parameter(
     *         name="amount",
     *         in="query",
     *         description="",
     *         required= true,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *      @OA\Parameter(
     *         name="service_id",
     *         in="query",
     *         description="pass array [1,2]",
     *         required= true,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Success",
     *         @OA\MediaType(
     *             mediaType="application/json",
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Invalid request"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="not found"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Unprocessable entity"
     *     ),
     * )
    **/
    public function studioBook(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'studio_id' => 'required|exists:studios,id',
            'start_time' => 'required',
            'end_time' => 'required',
            'date' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->response([], $validator->errors()->first(), false, 404);
        }

        try{
            $booking = new Booking;
            $booking->booking_id = '#' . rand(10, 99) . time();
            $booking->user_id = Auth::id();
            $booking->studio_id = $request->studio_id;
            $booking->booking_date = $request->date;
            $booking->start_time = $request->start_time;
            $booking->end_time = $request->end_time;
            $booking->total_hour = $request->total_hour;
            $booking->discount = $request->discount;
            $booking->fees = $request->fees;
            $booking->amount = $request->amount;
            $booking->save();
            
            
            $studio = Studio::find($booking->studio_id);
            $studio_user = User::find($studio->user_id);
            $booking_user = User::find($booking->user_id);
            if(isset($studio_user) && $studio_user->is_notification == 1)
            {
                sendPushNotification($studio_user->device_token, 'Studio booking',$booking_user->name . ' has booked ' . $studio->title. ' for ' . $booking->booking_date . '.'  ,1,$studio_user->id,$booking->studio_id, $booking->id);
            }

            if(isset($request->service_id) && count($request->service_id) > 0)
            {
                foreach($request->service_id as $serviceId){
                    $bookingService = new BookingService;
                    $bookingService->booking_id = $booking->id;
                    $bookingService->studio_service_id	 = $serviceId;
                    $serviceDetail = StudioService::find($serviceId);
                    $bookingService->name = $serviceDetail->name;
                    $bookingService->fees = $serviceDetail->fees;
                    $bookingService->description = $serviceDetail->description;
                    $bookingService->save();
                }
                $booking = Booking::with('book_services')->find($booking->id);
            }

           
           
            // generate stripe seesion id1

            Stripe::setApiKey(env('STRIPE_SECRATE_KEY'));
            $stripe = new StripeClient(env('STRIPE_SECRATE_KEY'));
            $session = $stripe->checkout->sessions->create([
                'payment_method_types'=> ['card'],
                'line_items' => [
                  [
                    'price_data'  => [
                        'product_data' => [
                            'name' => 'Studio booking',
                            'images' => ['https://i.imgur.com/EHyR2nP.png'],
                        ],
                        'unit_amount'  => 100 * 100,
                        'currency'     => 'usd',
                    ],
                    'quantity'    => 1,
                    'description' => 'test',
                  ],
                ],
                'mode' => 'payment',
                'success_url' => 'https://checkout.stripe.dev/success',
                'cancel_url' => 'https://checkout.stripe.dev/cancel',
              ]);
              $booking->session = $session;
            return $this->response($booking);

        }catch(Exception $e){
            return $this->response([], $e->getMessage(), false, 400);
        } 
    }

    /**
     *  @OA\Post(
     *     path="/api/customer/transaction",
     *     tags={"Customer"},
     *     security={{"bearer_token":{}}},  
     *     summary="Customer Transaction",
     *     operationId="customer-transaction",
     * 
     * 
     *     @OA\Parameter(
     *         name="booking_id",
     *         in="query",
     *         description="",
     *         required= true,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *      @OA\Parameter(
     *         name="transaction_id",
     *         in="query",
     *         description="",
     *         required= true,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *      @OA\Parameter(
     *         name="amount",
     *         in="query",
     *         description="",
     *         required= true,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *      @OA\Parameter(
     *         name="status",
     *         in="query",
     *         description="1 - success | 2 -decline",
     *         required= true,
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Success",
     *         @OA\MediaType(
     *             mediaType="application/json",
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Invalid request"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="not found"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Unprocessable entity"
     *     ),
     * )
    **/
    public function customerTransaction(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'booking_id' => 'required|exists:bookings,id',
            'transaction_id' => 'required',
            'amount' => 'required',
            'status' => 'required|in:1,2',
        ]);

        if ($validator->fails()) {
            return $this->response([], $validator->errors()->first(), false, 404);
        }

        try{
            $transaction = new BookingTransaction;
            $transaction->booking_id = $request->booking_id;
            $transaction->transaction_id = $request->transaction_id;
            $transaction->amount = $request->amount;
            $transaction->status = $request->status;
            $transaction->save();
            
            $bookings = auth()->user();
            $BookDetail = Booking::find($request->booking_id);
            // return $BookDetail;
            $UEmail = $bookings->email;
            $StudioDetail = Studio::find($BookDetail->studio_id);
            $Users = User::find($StudioDetail->user_id);
            $data = [
                'booking_date' => $BookDetail->booking_date,
                'booking_id' => $BookDetail->booking_id,
                'username' => $bookings->name,
                'email' => $bookings->email,
                'address' => $bookings->address,
                'city' => $bookings->city,
                'start_time' => $BookDetail->start_time,
                'end_time' => $BookDetail->end_time,
                'total_hour' => $BookDetail->total_hour,
                'discount' => $BookDetail->discount,
                'fees' => $BookDetail->fees,
                'amount' => $BookDetail->amount,
                'title' => $StudioDetail->title,
                'price' => $StudioDetail->price,
            ];
           Mail::send('mail.BookingDetailMail', $data, function ($message) use ($UEmail) {
                $message->to($UEmail, 'Studio-Tonight')->subject
                    ('Booking Detail');
            });


            $booking = Booking::find($request->booking_id);
            if($request->status == 1){
                $booking->status = 1;
                $booking->save();
                return $this->response([], 'Booking Successfully!');
            }else{
                $booking->status = 2;
                $booking->save();
                return $this->response([], 'Transaction decline please try again!');
            }
            
        }catch(Exception $e){
            return $this->response([], $e->getMessage(), false, 400);
        } 
    }    

}
