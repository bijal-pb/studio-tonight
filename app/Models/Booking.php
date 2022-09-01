<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use DB;


class Booking extends Model
{
    use HasFactory, SoftDeletes;

    public function book_by()
    {
        return $this->hasOne(User::class, 'id', 'user_id')->select(['id','name','email','avatar','firebase_id']);
    }

    public function book_services()
    {
        return $this->hasMany(BookingService::class, 'booking_id');
    }

    public function studio()
    {
        return $this->hasOne(Studio::class, 'id', 'studio_id')->with('owner');
    }

    public function studio_services()
    {
        return $this->hasMany(BookingService::class, 'booking_id')->with('service');
    }

    public function reviews()
    {
        return $this->hasMany(Review::class, 'booking_id')->where('type',1)->with('review_by');
    }

    public function feedbacks()
    {
        return $this->hasMany(Review::class, 'booking_id')->where('type',2)->with('feedback_by');
    }
}
