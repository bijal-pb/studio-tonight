<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Type;
use App\Models\Booking;
use App\Models\StudioPhoto;
use Auth;
use DB;

class Studio extends Model
{
    use HasFactory, SoftDeletes;

    protected $appends = ['is_favourite','rating','studio_distance'];

    public function studio_photos()
    {
        return $this->hasMany(StudioPhoto::class, 'studio_id', 'id')->select('id','studio_id','photo');
    }

    public function studio_type()
    {
        return $this->hasOne(Type::class,'id','type_id')->select('id','name','image');
    }
    public function studio_amenities()
    {
        return $this->hasMany(StudioAmenities::class, 'studio_id', 'id')->select('id','studio_id','amenities_id')->with('amenities');
    }

    public function amenities()
    {
        return $this->hasMany(StudioAmenities::class, 'studio_id', 'id')->select('id','studio_id','amenities_id');
    }
    public function ratings()
    {
        return $this->hasMany(Review::class, 'rating_to', 'id')->where('type',1)->with('review_by')->select('*');
    }

    public function services()
    {
        return $this->hasMany(StudioService::class, 'studio_id', 'id')->select('id','studio_id','name','fees','description');
    }

    public function timing()
    {
        return $this->hasOne(StudioTiming::class, 'studio_id', 'id')->select('*');
    }

    public function owner()
    {
        return $this->hasOne(User::class, 'id', 'user_id')->select('id','name','email','firebase_id','avatar');
    }


    public function getIsFavouriteAttribute()
    {
        $favourite = StudioFavourite::where('studio_id',$this->id)->where('customer_id',Auth::id())->first();
        if(isset($favourite)){
        return 1;
        }else {
        return 0;
        }

    }

    public function studio_booking()
    {
        return $this->hasMany(Booking::class,'studio_id','id')->select('*');
    }

    public function getRatingAttribute()
    {
        // return 5;
        return $this->hasMany(Review::class,'rating_to','id')->avg('rating') == null ? 0 : ceil($this->hasMany(Review::class,'rating_to','id')->avg('rating'));
    }

    public function studioUser()
    {
        return $this->hasOne(StudioUser::class, 'studio_id', 'id')->select('*')->with('verification');
    }
    public function verification_image()
    {
        return $this->hasMany(StudioDocument::class, 'studio_id', 'id')->select('*');
    }

    public function getStudioDistanceAttribute()
    {
        if (Auth::check()){
            if(Auth::user()->lat != null && Auth::user()->lang != null){
                $latitude = Auth::user()->lat; 
                $longitude = Auth::user()->lang;
                $studio = Studio::select(DB::Raw("(6371 * acos( cos( radians('$latitude') ) * cos( radians(lat) ) * cos( radians(lang) - radians('$longitude') ) + sin( radians('$latitude') ) * sin( radians(lat) ) ) ) AS distance"))->where('id',$this->id)->first();
                return round($studio->distance * 1.609344);
            }
        }
        return null;
    }

}
