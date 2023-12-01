<?php

namespace App\Models;

use App\Models\Vendor;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Category extends Model
{
    use HasFactory;
    protected $guarded = [];
    
    public function profile()
    {
        return $this->belongsTo(Profile::class);
    }

    public function logs()
    {
        return $this->morphToMany(Log::class, 'loggable');
    }

    public function vendor()
    {
        return $this->belongsToMany(Vendor::class);
    }
}
