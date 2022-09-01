<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudioAmenities extends Model
{
    use HasFactory;

    public function amenities()
    {
        return $this->hasOne(Amenities::class,'id','amenities_id')->select('id','name');
    }
}
