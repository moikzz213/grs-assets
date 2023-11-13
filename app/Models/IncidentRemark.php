<?php

namespace App\Models;

use App\Models\Profile;
use App\Models\Incident;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class IncidentRemark extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function incident()
    {
        return $this->belongsTo(Incident::class);
    }

    public function profile()
    {
        return $this->belongsTo(Profile::class);
    }
}
