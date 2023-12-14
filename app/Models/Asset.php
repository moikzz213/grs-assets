<?php

namespace App\Models;

use App\Models\File;

use App\Models\Vendor;
use App\Models\Company;
use App\Models\Category;
use App\Models\Location;
use App\Models\Status;
use App\Models\Warranty;
use App\Models\RequestAssetDetail;
use App\Models\AllottedInformation;
use App\Models\FinancialInformation;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Asset extends Model
{
    use HasFactory;

    protected $guarded = [];

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

    public function warranties()
    {
        return $this->hasMany(Warranty::class)->orderBy('id','DESC');
    }

    public function pivot_warranties(){
        return $this->belongsToMany(Warranty::class)->orderBy('id','DESC');
    }

    public function warranty_latest()
    {
        return $this->hasMany(Warranty::class)->orderBy('id','DESC')->limit(2);
    }

    public function status()
    {
        return $this->belongsTo(Status::class);
    }

    public function condition()
    {
        return $this->belongsTo(Status::class)->where('type', '=','condition-type');
    }

    public function maintenance()
    {
        return $this->hasMany(Incident::class)->orderBy('id', 'DESC')->where('type_id',2);
    }

    public function incidents()
    {
        return $this->hasMany(Incident::class)->orderBy('id', 'DESC')->whereNot('type_id',2);
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
        return $this->hasMany(AllottedInformation::class)->orderBy('id', 'DESC');
    }

    public function allotted_information_latest()
    {
        return $this->hasMany(AllottedInformation::class)->orderBy('id','DESC')->limit(1);
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

    public function financial_information()
    {
        return $this->hasOne(FinancialInformation::class);
    }


    public function items()
    {
        return $this->belongsToMany(RequestAssetDetail::class);
    }

}
