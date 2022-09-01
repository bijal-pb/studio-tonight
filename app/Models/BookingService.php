<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BookingService extends Model
{
    use HasFactory;

    public function service()
    {
        return $this->hasOne(StudioService::class, 'id', 'studio_service_id');
    }
}
