<?php

namespace App\Models;

use App\Models\Asset;
use App\Models\Incident;
use App\Models\Warranty;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class File extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function incidents()
    {
        return $this->morphedByMany(
            Incident::class,
            'fileable',
            'fileables',
            'file_id',
            'fileable_id',
            'id',
            'id',
        );
    }

    public function files()
    {
        return $this->morphedByMany(
            Asset::class,
            'fileable',
            'fileables',
            'file_id',
            'fileable_id',
            'id',
        );
    }

    public function warranty()
    {
        return $this->morphedByMany(
            Warranty::class,
            'fileable',
            'fileables',
            'file_id',
            'fileable_id',
            'id',
            'id',
        );
    }

    public function request_details()
    {
        return $this->belongsToMany(RequestAssetDetail::class);
    }
}
