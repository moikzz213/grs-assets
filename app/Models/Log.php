<?php

namespace App\Models;

use App\Models\Profile;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Log extends Model
{
    use HasFactory;
    protected $guarded = []; 

    public function profile()
    {
        return $this->morphedByMany(Profile::class, 'loggable');
    }
}
