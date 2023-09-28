<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class Companies extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $query = new \App\Models\Company([
            'title' => 'GHASSAN ABOUD GROUP FZE',
            'code'      => 'GAG', 
        ]);
        $query->save();
        $query = new \App\Models\Company([
            'title' => 'GAELAN MEDICAL TRADE LLC',
            'code'      => 'GMT', 
        ]);
        $query->save();
        $query = new \App\Models\Company([
            'title' => 'GRANDIOSE SUPERMARKET LLC',
            'code'      => 'GRSM', 
        ]);
        $query->save();
        $query = new \App\Models\Company([
            'title' => 'GHASSAN ABOUD CAR TRADING LTD',
            'code'      => 'GAC', 
        ]);
        $query->save();
        $query = new \App\Models\Company([
            'title' => 'GRANDIOSE CATERING LLC',
            'code'      => 'GRSC', 
        ]);
        $query->save();
        $query = new \App\Models\Company([
            'title' => 'GALLEGA GLOBAL LOGISTICS LLC',
            'code'      => 'GGL', 
        ]);
        $query->save();
        $query = new \App\Models\Company([
            'title' => 'GA TRADE PLATFORMS BUYGRO',
            'code'      => 'BUYGRO', 
        ]);
        $query->save();
        $query = new \App\Models\Company([
            'title' => 'LIVE POINT ART PRODUCTION LLC',
            'code'      => 'LP', 
        ]);
        $query->save();
        $query = new \App\Models\Company([
            'title' => 'ELEMENTS PROPERTIES LIMITED',
            'code'      => 'EPL', 
        ]);
        $query->save();
        $query = new \App\Models\Company([
            'title' => 'OLIVE COUNTRY GENERAL TRADING',
            'code'      => 'OCGT', 
        ]);
        $query->save();
        $query = new \App\Models\Company([
            'title' => 'GAELAN MEDICAL CARE LLC',
            'code'      => 'MEDCARE', 
        ]);
        $query->save();
        $query = new \App\Models\Company([
            'title' => 'GA AUTO PLATFORMS BUYPARTS',
            'code'      => 'BUYPARTS', 
        ]);
        $query->save();
        $query = new \App\Models\Company([
            'title' => 'GHASSAN ABOUD AUTO SP LLC',
            'code'      => 'GACSP', 
        ]);
        $query->save();

    }
}
