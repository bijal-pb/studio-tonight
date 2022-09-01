<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    use HasFactory;

    public function review_by()
    {
        return $this->hasOne(User::class, 'id','rating_by');
    }

    public function feedback_by()
    {
        return $this->hasOne(Studio::class, 'id','rating_by')->with('studio_photos');
    }

    
}
