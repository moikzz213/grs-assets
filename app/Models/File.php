<?php

namespace App\Models;

use App\Models\Incident;
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
}
