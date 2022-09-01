<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudioUser extends Model
{
    use HasFactory;

    public function verification()
    {
        return $this->hasOne(Verification::class, 'id', 'verification_id')->select('id','name');
    }

}
