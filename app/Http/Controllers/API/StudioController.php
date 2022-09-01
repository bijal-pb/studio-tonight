<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Studio;
use App\Models\StudioService;
use App\Models\StudioAmenities;
use App\Models\StudioDocument;
use App\Models\StudioPhoto;
use App\Models\StudioTiming;
use App\Models\StudioUser;
use App\Models\Type;
use App\Models\Review;
use App\Models\Verification;
use Illuminate\Support\Facades\Validator;
use App\Traits\ApiTrait;
use Exception;
use DB;
use Auth;
use Illuminate\Support\Carbon;


class StudioController extends Controller
{
    use ApiTrait;
    /**
     *  @OA\Post(
     *      path="/api/studio/add",
     *     tags={"Studio"},
     *     summary="Studio Add",
     *     security={{"bearer_token":{}}},
     *     operationId="studio-add",
     *   
     *     @OA\Parameter(
     *         name="title",
     *         required=true,
     *         in="query",
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="type_id",
     *         required=true,
     *         in="query",
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="price",
     *         required=true,
     *         in="query",
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="description",
     *         required=true,
     *         in="query",
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="rules",
     *         required=true,
     *         in="query",
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *      @OA\Parameter(
     *         name="cancel_policy",
     *         required=true,
     *         in="query",
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *      @OA\Parameter(
     *         name="refund_policy",
     *         required=true,
     *         in="query",
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *      @OA\Parameter(
     *         name="availability",
     *         required=true,
     *         in="query",
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *      @OA\Parameter(
     *         name="lat",
     *         required=true,
     *         in="query",
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *      @OA\Parameter(
     *         name="lang",
     *         required=true,
     *         in="query",
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *      @OA\Parameter(
     *         name="address",
     *         required=true,
     *         in="query",
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="city",
     *         required=true,
     *         in="query",
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *      @OA\Parameter(
     *         name="state",
     *         required=true,
     *         in="query",
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *      @OA\Parameter(
     *         name="country",
     *         required=true,
     *         in="query",
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *      @OA\Parameter(
     *         name="area",
     *         required=true,
     *         in="query",
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="amenities",
     *         required=true,
     *         in="query",
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="photo",
     *         required=true,
     *         in="query",
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="services",
     *         required=true,
     *         in="query",
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="user",
     *         in="query",
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

    public function studioAdd(Request $request)
    {
        DB::beginTransaction();
        try{
            $studio = new Studio;
            $studio->title = $request->title;
            $studio->user_id = Auth::id();
            $studio->type_id = $request->type_id;
            $studio->price = $request->price;
            $studio->description = $request->description;
            $studio->rules = $request->rules;
            $studio->cancel_policy = $request->cancel_policy;
            $studio->refund_policy = $request->refund_policy;
            $studio->availability = $request->availability;
            $studio->lat = $request->lat;
            $studio->lang = $request->lang;
            $studio->address = $request->address;
            $studio->city = $request->city;
            $studio->state = $request->state;
            $studio->country = $request->country;
            $studio->area = $request->area;
            $studio->save();
            
            // add studio amenitites
            if(is_array($request->amenities)){
                foreach($request->amenities as $amenities){
                    $this->studioAmenities($studio->id,$amenities);
                }
            }
            // add studio timing
            $this->studioTiming($studio->id,$request->timing);

            // add studio photo
            if(is_array($request->photos)){
                foreach($request->photos as $p){
                    $this->studioPhoto($studio->id, $p['path']);
                }
            }
            
            // add studio service
            if(is_array($request->services)){
                foreach($request->services as $service){
                    $this->studioService($studio->id,$service);
                }
            }

            // add studio user
            $this->studioUser($studio->id,$request->user);
            DB::commit();
            return $this->response($studio, 'Studio created successfully!');
        }catch(Exception $e){
            DB::rollback();
            return $this->response([], $e->getMessage(), false, 400);
        }
    }

    private function studioAmenities($studioId, $amenitiesId)
    {
        $sa = new StudioAmenities;
        $sa->studio_id = $studioId;
        $sa->amenities_id = $amenitiesId;
        $sa->save();
    }

    private function studioTiming($studioId, $timing)
    {
        $st = new StudioTiming;
        $st->studio_id = $studioId;
        $st->advance_booking = $timing['advance_booking'];
        $st->min_booking = $timing['min_booking'];
        $st->max_capacity = $timing['max_capacity'];
        $st->is_24_hour = isset($timing['is_24_hour']) ? $timing['is_24_hour'] : 0;
        if(isset($timing['hour_24']) && $timing['hour_24'] == 1){
            $st->mon = 1;
            $st->mon_start_time = "00:00:01";
            $st->mon_end_time = "24:00:00";
            
            $st->tue = 1;
            $st->tue_start_time = "00:00:01";
            $st->tue_end_time = "24:00:00";
            
            $st->wed = 1;
            $st->wed_start_time = "00:00:01";
            $st->wed_end_time = "24:00:00";

            $st->thu = 1;
            $st->thu_start_time = "00:00:01";
            $st->thu_end_time = "24:00:00";
        
            $st->fri = 1;
            $st->fri_start_time = "00:00:01";
            $st->fri_end_time = "24:00:00";
            
            $st->sat = 1;
            $st->sat_start_time = "00:00:01";
            $st->sat_end_time = "24:00:00";

            $st->sun = 1;
            $st->sun_start_time = "00:00:01";
            $st->sun_end_time = "24:00:00";
        }else{
            $st->mon = $timing['mon'];
            if($timing['mon'] == 1){
                $st->mon_start_time = $timing['mon_start_time'];
                $st->mon_end_time = $timing['mon_end_time'];
            }
            
            $st->tue = $timing['tue'];
            if($timing['tue'] == 1){
                $st->tue_start_time = $timing['tue_start_time'];
                $st->tue_end_time = $timing['tue_end_time'];
            }
            
            $st->wed = $timing['wed'];
            if($timing['wed'] == 1){
                $st->wed_start_time = $timing['wed_start_time'];
                $st->wed_end_time = $timing['wed_end_time'];
            }

            $st->thu = $timing['thur'];
            if($timing['thur'] == 1){
                $st->thu_start_time = $timing['thur_start_time'];
                $st->thu_end_time = $timing['thur_end_time'];
            }
        
            $st->fri = $timing['fri'];
            if($timing['fri'] == 1){
                $st->fri_start_time = $timing['fri_start_time'];
                $st->fri_end_time = $timing['fri_end_time'];
            }
            
            $st->sat = $timing['sat'];
            if($timing['sat'] == 1){
                $st->sat_start_time = $timing['sat_start_time'];
                $st->sat_end_time = $timing['sat_end_time'];
            }

            $st->sun = $timing['sun'];
            if($timing['sun'] == 1){
                $st->sun_start_time = $timing['sun_start_time'];
                $st->sun_end_time = $timing['sun_end_time'];
            }
        }
        $st->save();
    }

    private function studioPhoto($studioId, $path)
    {
        $sp = new StudioPhoto;
        $sp->studio_id = $studioId;
        $sp->photo = $path;
        $sp->save();
    }

    private function studioService($studioId, $service)
    {
        $studService = new StudioService;
        $studService->studio_id = $studioId;
        $studService->name = $service['name'];
        $studService->fees = $service['fees'];
        $studService->description = $service['description'];
        $studService->save();
    }

    private function studioUser($studioId,$user)
    {   
        $su = new StudioUser;
        $su->studio_id = $studioId;
        $su->name = $user['name'];
        $su->phone = $user['phone'];
        $su->email = $user['email'];
        $su->verification_id = $user['verification_id'];
        $su->about = $user['about'];
        $su->save();
        if(isset($user['documents']) && is_array($user['documents'])){
            foreach($user['documents'] as $document){
                $sd = new StudioDocument;
                $sd->studio_id = $studioId;
                $sd->studio_user_id = $su->id;
                $sd->image = $document['image'];
                $sd->save();                              
            }
        }
    }

    public function studioHome(Request $request)
    {
        $studios = Studio::with(['studio_type','studio_photos'])->where('user_id',Auth::id())->get();
        return $this->response($studios, 'Home!');
    }

    /**
     *  @OA\Get(
     *      path="/api/get/studio",
     *     tags={"Studio"},
     *     summary="studio",
     *     security={{"bearer_token":{}}},
     *     operationId="customer-home",
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
    public function studios(Request $request)
    {
    try{
        $studios = Studio::select('id','title')->where('user_id',Auth::id())->get();
        
        return $this->response($studios);
    }catch(Exception $e){
        return $this->response([], $e->getMessage(), false, 400);
    }
    }


    /**
     *  @OA\Get(
     *      path="/api/customer/home",
     *     tags={"Customer"},
     *     summary="customer-home",
     *     operationId="customer-home",
     * 
     *     @OA\Parameter(
     *         name="lat",
     *         required=true,
     *         in="query",
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="lang",
     *         required=true,
     *         in="query",
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
    public function customerHome(Request $request)
    {
        $validator = Validator::make($request->all(),[
			'lat' => 'required',
			'lang' => 'required',
		]);

		if($validator->fails())
		{
			return $this->response($validator->errors()->first(), false,400);
		}
		try{
			$latitude = $request->lat;
			$longitude = $request->lang;
			$distance = 30;
			$cities = Studio::whereRaw("(6371 * acos( cos( radians('$latitude') ) * cos( radians(lat) ) * cos( radians(lang) - radians('$longitude') ) + sin( radians('$latitude') ) * sin( radians(lat) ) ) ) <= $distance")
					->where('is_active',1)->pluck('city')->toArray();    
            $home = [];
            $home['slider'] = [
                ["id" => 1, "image" => asset('/slider/slider1.png')],
                ["id" => 2, "image" => asset('/slider/slider1.png')],
                ["id" => 3, "image" => asset('/slider/slider1.png')],
                ["id" => 4, "image" => asset('/slider/slider1.png')]
            ];
            $home['cities'] = array_unique($cities);
            $home['types'] = Type::select('id','name','image')->get();
            $home['top_rated'] = Studio::with(['studio_photos','studio_type'])->where('is_active',1)->limit(10)->get();

			return $this->response($home,'Customer Home');
		}catch(Exception $e){
			return $this->response([], $e->getMessage(), false, 400);
		}
    }

    /**
     *  @OA\Get(
     *      path="/api/customer/studios",
     *     tags={"Customer"},
     *     summary="studioList",
     *     operationId="studioList",
     * 
     *     @OA\Parameter(
     *         name="lat",
     *         in="query",
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="lang",
     *         in="query",
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="type",
     *         in="query",
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="city",
     *         in="query",
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *      @OA\Parameter(
     *         name="amenities",
     *         in="query",
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="area",
     *         in="query",
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),@OA\Parameter(
     *         name="min_price",
     *         in="query",
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="max_price",
     *         in="query",
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *      @OA\Parameter(
     *         name="date",
     *         in="query",
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="start_time",
     *         in="query",
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="end_time",
     *         in="query",
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="guests",
     *         in="query",
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *   @OA\Parameter(
     *         name="rate",
     *         in="query",
     *         description="true | false",
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
    public function studioList(Request $request)
    {
        
        try{
            $studios = Studio::with(['studio_photos','studio_type','studio_amenities','timing'])
                                ->select('studios.*','timings.advance_booking','timings.max_capacity')
                                ->leftJoin('studio_timings as timings','studios.id','=','timings.studio_id')
                                ->where('studios.is_active',1);

            if(isset($request->date) && $request->date != null)
            {
                $day = strtolower(Carbon::parse($request->date)->format('D'));
                $studios = $studios->where('timings.'.$day,1);
                if(isset($request->start_time) && $request->start_time != null){
                    $studios = $studios->where('timings.'.$day.'_start_time','<=',$request->start_time);
                }
                if(isset($request->end_time) && $request->start_time != null){
                    $studios = $studios->where('timings.'.$day.'_end_time','>=', $request->end_time);
                }
            }
            if(isset($request->guests) && $request->guests != null)
            {
                $studios = $studios->where('timings.max_capacity','>=', $request->guests);
            }

            if(isset($request->lat) && $request->lat != null && isset($request->lang) && $request->lang != null){
                $distance = 30;
                $latitude = $request->lat;
                $longitude = $request->lang;
                $studios = $studios->whereRaw("(6371 * acos( cos( radians('$latitude') ) * cos( radians(lat) ) * cos( radians(lang) - radians('$longitude') ) + sin( radians('$latitude') ) * sin( radians(lat) ) ) ) <= $distance");
            }
            
            if(isset($request->area) && $request->area != null){
                $distance = $request->area;
                $latitude = Auth::user()->lat;
                $longitude = Auth::user()->lang;
                if($latitude != null && $longitude != null){
                    $studios = $studios->whereRaw("(6371 * acos( cos( radians('$latitude') ) * cos( radians(lat) ) * cos( radians(lang) - radians('$longitude') ) + sin( radians('$latitude') ) * sin( radians(lat) ) ) ) <= $distance");
                }
            }
            if(isset($request->type) && $request->type != null){
                $studios = $studios->where('studios.type_id', $request->type);
            }
            if(isset($request->city) && $request->city != null){
                $studios = $studios->where('studios.city', $request->city);
            }
            if(isset($request->min_price) && $request->min_price != null && isset($request->max_price) && $request->max_price != null){
                $studios = $studios->where('studios.price','>=', $request->min_price)
                                    ->where('studios.price','<=', $request->max_price);
            }
            
            if(isset($request->amenities) && $request->amenities != null){
                $i = 1;
                $amenities = explode(',', $request->amenities);
                foreach($amenities as $am){
                    $ame[$i] = StudioAmenities::where('amenities_id',$am)->pluck('studio_id')->toArray();
                    $i++;
                }
                for($j = 1; $j < $i; $j++)
                {
                    if($j == 1){
                        $result = $ame[1];
                    }else{
                        $result = array_intersect($result, $ame[$j]);
                    }
                }
                $studios = $studios->whereIn('studios.id', $result);
            }
            if(isset($rquest->date) && $request->date != null){
                $studios = $studios->where('studios.price','>=', $request->min_price);
            }
            if(isset($request->rate) && $request->rate == 1 ){
                $studios =  $studios->get()->sortByDesc('rating')->take(10)->values();
            }else{
                $studios = $studios->paginate(50);
            }
            return $this->response($studios,'Studios List');
        }catch(Exception $e){
			return $this->response([], $e->getMessage(), false, 400);
		}
    }


     /**
     *  @OA\Get(
     *     path="/api/studio/detail",
     *     tags={"Studio"},
     *     summary=" Studio Detail",
     *     operationId="studio-detail",
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
    public function studioDetail(Request $request)
    {
        $validator = Validator::make($request->all(),[
			'studio_id' => 'required|exists:studios,id',
		]);

		if($validator->fails())
		{
			return $this->response($validator->errors()->first(), false,400);
		}
        try{
            $studio = Studio::with(['studio_photos','studio_type','studio_amenities','services', 'ratings', 'timing','owner'])->where('id', $request->studio_id)->first();
            return $this->response($studio,'Studio Detail!');
        }catch(Exception $e){
			return $this->response([], $e->getMessage(), false, 400);
		}
        
    }

    /**
     *  @OA\Post(
     *      path="/api/customer/review",
     *     tags={"Customer"},
     *     summary="review",
     *     security={{"bearer_token":{}}},
     *     operationId="customer-review",
     * 
     *    @OA\Parameter(
     *         name="booking_id",
     *         required=true,
     *         in="query",
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *   @OA\Parameter(
     *         name="type",
     *         in="query",
     *         description="1 -  customer to studio | 2 - studio to customer",
     *         required=true,
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     * 
     *     @OA\Parameter(
     *         name="rating_by",
     *         required=true,
     *         in="query",
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="rating_to",
     *         required=true,
     *         in="query",
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *      @OA\Parameter(
     *         name="rating",
     *         required=true,
     *         in="query",
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *      @OA\Parameter(
     *         name="comment",
     *         required=true,
     *         in="query",
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
    public function review(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'booking_id' => 'required|exists:bookings,id',
			'rating_by' => 'required',
            'rating_to' => 'required',
            'type' => 'required|in:1,2',
            'rating' => 'required',
		]);

		if($validator->fails())
		{
			return $this->response($validator->errors()->first(), false,400);
		}
        try{
            $review = new Review();
            $review->booking_id = isset($request->booking_id) ? $request->booking_id : null;
            $review->rating_by = $request->rating_by;
            $review->rating_to = $request->rating_to;
            $review->rating = $request->rating;
            $review->type = $request->type;
            $review->comment = $request->comment;
            $review->save();
            return $this->response($review,'Review added successfully!');
        }catch(Exception $e){
			return $this->response([], $e->getMessage(), false, 400);
		}
    }

     /**
     *  @OA\Get(
     *     path="/api/verification/list",
     *     tags={"Studio"},
     *     summary="Get verifications List",
     *     operationId="List-verifications",
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
    public function verificationList(Request $request)
    {
        $verification = Verification::select('id','name')->get();
        return $this->response($verification, 'Verification List!'); 
    }


    /**
     *  @OA\Post(
     *      path="/api/studio/delete",
     *      tags={"Studio"},
     *      summary="Studio Delete",
     *      security={{"bearer_token":{}}},
     *      operationId="studio-delete",
     * 
     *     @OA\Parameter(
     *         name="studio_id",
     *         required=true,
     *         in="query",
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
    public function studioDelete(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'studio_id' => 'required|exists:studios,id',
		]);

		if($validator->fails())
		{
			return $this->response($validator->errors()->first(), false,400);
		}
        try{
            $studio = Studio::find($request->studio_id);
            if($studio->user_id != Auth::id()){
                return $this->response('You have no permission!', false,400);
            }
            $studio->delete();
            return $this->response([], 'Deleted Successfully!');
        }catch(Exception $e){
			return $this->response([], $e->getMessage(), false, 400);
		}
    }


    /**
     *  @OA\Get(
     *     path="/api/studio/search",
     *     tags={"Customer"},
     *     summary="Studio serach",
     *     operationId="Studio-serach",
     * 
     *     @OA\Parameter(
     *         name="search",
     *         required=true,
     *         in="query",
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
    public function studioSearch(Request $request)
    {
        $validator = Validator::make($request->all(),[
			'search' => 'required',
		]);

		if($validator->fails())
		{
			return $this->response($validator->errors()->first(), false,400);
		}
        try{
            $studios = Studio::with(['studio_photos','studio_type','studio_amenities','services', 'ratings', 'timing','owner'])
                    ->where('title', 'Like', '%'.$request->search.'%')
                    ->where('is_active',1)
                    ->get();
            return $this->response($studios,'Studios!');
        }catch(Exception $e){
			return $this->response([], $e->getMessage(), false, 400);
		}
    }

}
