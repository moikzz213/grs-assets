<?php

namespace App\Models;

use App\Models\Asset;
use App\Models\Location;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AllottedInformation extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function asset()
    {
        return $this->belongsTo(Asset::class);
    }

    public function location()
    {
        return $this->belongsTo(Location::class);
    }
}
