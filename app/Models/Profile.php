<?php

namespace App\Models;

use App\Models\Log;
use App\Models\Asset;
use App\Models\Access;
use App\Models\Company;
use App\Models\Incident;
use App\Models\Location;
use App\Models\ClientKey;
use App\Models\RequestAsset;
use App\Models\ApprovalStages;
use App\Models\IncidentRemark;
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

    public function location()
    {
        return $this->belongsTo(Location::class);
    }

    public function remarks()
    {
        return $this->belongsTo(IncidentRemark::class);
    }

    public function access()
    {
        return $this->hasMany(Access::class);
    }

    public function incident()
    {
        return $this->hasMany(Incident::class);
    }

    public function logs()
    {
        return $this->morphToMany(Log::class, 'loggable');
    }

    public function stage()
    {
        return $this->belongsToMany(ApprovalStages::class);
    }
    public function created_assets()
    {
        return $this->hasMany(Asset::class);
    }

    public function last_updated_assets()
    {
        return $this->hasMany(Asset::class);
    }

    public function reminder_profile()
    {
        return $this->hasMany(RequestAsset::class, 'reminder_profile_id');
    }
}
