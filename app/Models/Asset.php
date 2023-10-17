<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Asset extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function created_by()
    {
        return $this->belongsTo(Profile::class);
    }

    public function last_updated_by()
    {
        return $this->belongsTo(Profile::class);
    }
}
