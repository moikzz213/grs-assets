<?php

namespace App\Models;


use App\Models\RequestAsset;
use App\Models\Asset;
use App\Models\File;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class RequestAssetDetail extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function request_asset()
    {
        return $this->belongsTo(RequestAsset::class);
    }

    public function assets()
    {
        return $this->belongsTo(Asset::class);
    }

    public function attachment()
    {
        return $this->belongsTo(File::class, 'file_id');
    }
}
