<?php

namespace App\Models;

use App\Models\Asset;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class FinancialInformation extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function asset()
    {
        return $this->belongsTo(Asset::class);
    }

}
