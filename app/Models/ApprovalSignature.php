<?php

namespace App\Models;

use App\Models\ApprovalStages;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ApprovalSignature extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function stages()
    {
        return $this->belongsTo(ApprovalStages::class);
    } 
}
