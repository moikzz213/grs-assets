<?php

namespace App\Models;

use App\Models\Log;
use App\Models\File;
use App\Models\Asset;
use App\Models\Status;
use App\Models\Company;
use App\Models\Profile;
use App\Models\Location;
use App\Models\IncidentRemark;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Incident extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function attachment()
    {
        return $this->morphToMany(
            File::class,
            'fileable',
            'fileables',
            'fileable_id',
            'file_id',
            '',
            'id'
        );

    }

    public function logs()
    {
        return $this->morphToMany(Log::class, 'loggable');
    }

    public function remarks()
    {
        return $this->hasMany(IncidentRemark::class)->orderBy("id",'DESC');
    }

    public function asset()
    {
        return $this->belongsTo(Asset::class);
    }

    public function profile()
    {
        return $this->belongsTo(Profile::class);
    }

    public function handled_by()
    {
        return $this->belongsTo(Profile::class, 'handled_by');
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function location()
    {
        return $this->belongsTo(Location::class);
    }

    public function type()
    {
        return $this->belongsTo(Status::class, 'type_id', 'id');
    }

    public function status()
    {
        return $this->belongsTo(Status::class, 'status_id', 'id');
    }

    public function urgency()
    {
        return $this->belongsTo(Status::class, 'urgency_id', 'id');
    }
}
/**
 *
 *
 *
 *
 *
 * type_id
 * status_id
 */
