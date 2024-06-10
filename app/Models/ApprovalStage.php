<?php

namespace App\Models;

use App\Models\Log;
use App\Models\ApprovalSetup;
use App\Models\Profile;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ApprovalStage extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function approval_setup()
    {
        return $this->belongsTo(ApprovalSetup::class);
    }

    public function signatures()
    {
        return $this->belongsToMany(Profile::class);
    }

    public function logs()
    {
        return $this->morphToMany(Log::class, 'loggable');
    }
}
