<?php

namespace App\Models;

use App\Models\Asset;
 
use App\Models\Access;
use App\Models\Vendor;
use App\Models\Company;
use App\Models\Profile;
use App\Models\Category;
use App\Models\Incident;
use App\Models\Location;
 
use App\Models\ApprovalStages;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Log extends Model
{
    use HasFactory;
    protected $guarded = []; 

    public function profile()
    {
        return $this->morphedByMany(Profile::class, 'loggable');
    }

    public function access()
    {
        return $this->morphedByMany(Access::class, 'loggable');
    }

    public function company()
    {
        return $this->morphedByMany(Company::class, 'loggable');
    }

    public function location()
    {
        return $this->morphedByMany(Location::class, 'loggable');
    } 

    public function category()
    {
        return $this->morphedByMany(Category::class, 'loggable');
    }

    public function vendor()
    {
        return $this->morphedByMany(Vendor::class, 'loggable');
    }

    public function asset()
    {
        return $this->morphedByMany(Asset::class, 'loggable');
    }

    public function incident()
    {
        return $this->morphedByMany(Incident::class, 'loggable');
    }

    public function approvalSetup()
    {
        return $this->morphedByMany(ApprovalSetup::class, 'loggable');
    }

    public function approvalStages()
    {
        return $this->morphedByMany(ApprovalStages::class, 'loggable');
    }
}
