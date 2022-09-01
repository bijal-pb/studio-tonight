<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudioFavourite extends Model
{
    use HasFactory;

    public function studio()
    {
        return $this->hasOne(Studio::class,'id','studio_id')->with(['studio_photos','studio_type','studio_amenities','ratings','services']);
    }
}
