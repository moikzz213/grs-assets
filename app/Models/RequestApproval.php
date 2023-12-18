<?php

namespace App\Models;

use App\Models\Log;
use App\Models\Profile;
use App\Models\RequestAsset;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class RequestApproval extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function logs()
    {
        return $this->morphToMany(Log::class, 'loggable');
    }

    
    public function request()
    {
        return $this->belongsTo(RequestAsset::class);
    }

    public function profile()
    {
        return $this->belongsTo(Profile::class, 'profile_id');
    }
}
