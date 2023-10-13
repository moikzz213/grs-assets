<?php

namespace App\Models;

use App\Models\Log;
use App\Models\Access;
use App\Models\Company;
use App\Models\ClientKey;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Profile extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function client_keys()
    {
        return $this->hasMany(ClientKey::class, 'username', 'username');
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function access()
    {
        return $this->hasMany(Access::class);
    }

    public function logs()
    {
        return $this->morphToMany(Log::class, 'loggable');
    }
}
