<?php

namespace App\Models;

use App\Models\Log;
use App\Models\Category;
use App\Models\Warranty;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Vendor extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function profile()
    {
        return $this->belongsTo(Profile::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function warranty()
    {
        return $this->hasMany(Warranty::class);
    }

    public function logs()
    {
        return $this->morphToMany(Log::class, 'loggable');
    }
}
