<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class Locations extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $query = new \App\Models\Location([
            'title' => 'Victory Heights',
            'code'      => 'DBVHS', 
        ]);
        $query->save();
        $query = new \App\Models\Location([
            'title' => 'Jumeirah 2',
            'code'      => 'DBJ2RS', 
        ]);
        $query->save();
        $query = new \App\Models\Location([
            'title' => 'Dubai Mall - Zabeel',
            'code'      => 'DBZAB', 
        ]);
        $query->save();
        $query = new \App\Models\Location([
            'title' => 'Sports City',
            'code'      => 'DBSPC', 
        ]);
        $query->save();
       
    }
}
