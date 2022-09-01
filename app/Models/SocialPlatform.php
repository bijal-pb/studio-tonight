<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SocialPlatform extends Model
{
    use HasFactory;

    protected $table = 'social_profile';
    public $timestamps = false;
}
