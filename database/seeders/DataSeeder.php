<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // vendor
        for ($i=0; $i <= 3 ; $i++) {
            $query = new \App\Models\Vendor([
                'title' => 'Test Vendor '.$i,
                'address' => 'Address '.$i,
                'contact_no' => '04000000'.$i,
                'contact_name' => 'Contact Name '.$i,
                'contact_email' => 'test'.$i.'@test.com',
            ]);
            $query->save();
        }

        // model
        for ($i=0; $i <= 3 ; $i++) {
            $query = new \App\Models\SpecModel([
                'title' => 'Test Model '.$i,
            ]);
            $query->save();
        }

        // brand
        for ($i=0; $i <= 3 ; $i++) {
            $query = new \App\Models\Brand([
                'title' => 'Test Brand '.$i,
            ]);
            $query->save();
        }

        // category
        for ($i=0; $i <= 3 ; $i++) {
            $query = new \App\Models\Category([
                'title' => 'Test Category '.$i,
                'code' => 'cat'.$i,
            ]);
            $query->save();
        }
    }
}
