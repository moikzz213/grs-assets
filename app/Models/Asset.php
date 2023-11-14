<?php

namespace App\Models;

use App\Models\File;
use App\Models\Brand;
use App\Models\Vendor;
use App\Models\Company;
use App\Models\Category;
use App\Models\Location;
use App\Models\Status;
use App\Models\Warranty;
use App\Models\SpecModel;
use App\Models\Maintenance;
use App\Models\RequestAssetDetail;
use App\Models\AllottedInformation;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Asset extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

    public function model()
    {
        return $this->belongsTo(SpecModel::class);
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function location()
    {
        return $this->belongsTo(Location::class);
    }

    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function warranty()
    {
        return $this->hasMany(Warranty::class);
    }

    public function status()
    {
        return $this->belongsTo(Status::class)->where('type','=', 'asset');
    }

    public function condition()
    {
        return $this->belongsTo(Status::class)->where('type', '=','condition-type');
    }

    public function maintenance()
    {
        return $this->hasMany(Incident::class)->where('type_id',2);
    }
    public function created_by()
    {
        return $this->belongsTo(Profile::class, 'author_id');
    }

    public function last_updated_by()
    {
        return $this->belongsTo(Profile::class, 'last_author_id');
    }

    public function allotted_informations()
    {
        return $this->hasMany(AllottedInformation::class);
    }

    public function attachments()
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
    public function items()
    {
        return $this->belongsToMany(RequestAssetDetail::class);
    }

}
