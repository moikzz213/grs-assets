<?php

namespace App\Models;

use App\Models\Log;
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

    public function profile()
    {
        return $this->belongsTo(Profile::class, 'profile_id');
    }
}
