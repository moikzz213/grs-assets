<?php

namespace App\Models;

use App\Models\Log;
use App\Models\ApprovalStage;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ApprovalSetup extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function stages()
    {
        return $this->hasMany(ApprovalStage::class);
    }

    public function profile()
    {
        return $this->belongsTo(Profile::class);
    }

    public function logs()
    {
        return $this->morphToMany(Log::class, 'loggable');
    }
}
