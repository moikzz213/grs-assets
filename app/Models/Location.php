<?php

namespace App\Models;

use App\Models\AllottedInformation;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Location extends Model
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


    // public function allotted_to()
    // {
    //     return $this->hasMany(AllottedInformation::class);
    // }

    public function transferred_to()
    {
        return $this->hasMany(AllottedInformation::class);
    }
}
