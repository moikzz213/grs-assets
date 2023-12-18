<?php

namespace App\Models;

use App\Models\Log;
use App\Models\File;
use App\Models\Asset;
use App\Models\Vendor;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Warranty extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function logs()
    {
        return $this->morphToMany(Log::class, 'loggable');
    }

    public function asset()
    {
        return $this->belongsTo(Asset::class);
    }

    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }

    public function pivot_assets(){
        return $this->belongsToMany(Asset::class)->orderBy('id','DESC');
    }

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
}
