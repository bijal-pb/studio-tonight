<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Permission\Traits\HasRoles;
use App\Http\Controllers\Services\StripeService;
use Auth;


class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes, HasRoles;


    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'email',
        'password',
        'first_name',
        'last_name'
    ];

    public function status(){

        return $this->hasOne(User::class)->select('type');

    }

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];


    // public function getAvatarAttribute($value)
    // {
    //     if ($value) {
    //         return asset('/user/' . $value);
    //     } else {
    //         return null;
    //     }
    // }

    protected $appends = ['total_studios'];

    protected static function boot()
    {
        parent::boot();

        static::created(function ($user) {

            $stripe = new StripeService();
            $cust = $stripe->customer($user->email);
            if ($cust) {
                $user->stripe_id = $cust->id;
            }

            $user->save();
        });
    }

    public function getTotalStudiosAttribute()
    {
        return $this->hasMany(Studio::class,'user_id','id')->count();
    }
}
