<?php

namespace App\Models;

use App\Models\Profile;
use App\Models\ApprovalStages;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApprovalStageProfile extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function profile()
    {
        return $this->belongsTo(Profile::class);
    } 

    public function stages()
    {
        return $this->belongsTo(ApprovalStages::class);
    } 
}
