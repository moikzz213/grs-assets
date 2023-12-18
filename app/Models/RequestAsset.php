<?php

namespace App\Models;

use App\Models\Log;
use App\Models\ApprovalSetup;
use App\Models\RequestApproval;
use App\Models\RequestAssetDetail;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class RequestAsset extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function logs()
    {
        return $this->morphToMany(Log::class, 'loggable');
    }

    public function items()
    {
        return $this->hasMany(RequestAssetDetail::class);
    }

    public function request_approvals()
    {
        return $this->hasMany(RequestApproval::class);
    }

    public function setup()
    {
        return $this->belongsTo(ApprovalSetup::class, 'request_type_id');
    }

    public function profile()
    {
        return $this->belongsTo(Profile::class);
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function transfer_from()
    {
        return $this->belongsTo(Location::class, 'transferred_from');
    }

    public function transfer_to()
    {
        return $this->belongsTo(Location::class, 'transferred_to');
    }
}
