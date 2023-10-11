<?php

namespace App\Models;

use App\Models\ApprovalSignature;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ApprovalStages extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function approval_setup()
    {
        return $this->belongsTo(ApprovalSetup::class);
    }

    public function signatures()
    {
        return $this->hasMany(ApprovalSignature::class);
    }
}
