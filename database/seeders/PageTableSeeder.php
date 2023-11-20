<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PageTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $dataArr = array(
            array('title' => 'Users', 'slug' => 'users'),
            array('title' => 'Pages', 'slug' => 'pages'),
            array('title' => 'Companies', 'slug' => 'companies'),
            array('title' => 'Locations', 'slug' => 'locations'),
            array('title' => 'Categories', 'slug' => 'categories'),
            array('title' => 'Vendors', 'slug' => 'vendors'),
            array('title' => 'Models', 'slug' => 'models'),
            array('title' => 'Brands', 'slug' => 'brands'),
            array('title' => 'Assets', 'slug' => 'asset-list'),
            array('title' => 'Notifications', 'slug' => 'notifications'),
            array('title' => 'Others', 'slug' => 'others'),
            array('title' => 'Maintenance', 'slug' => 'maintenance'),
            array('title' => 'Import Asset', 'slug' => 'asset-list/import'),
        );

        foreach($dataArr AS $k => $v){ 
            $query = new \App\Models\Page([
                'title' => $v['title'],
                'slug'  => $v['slug'],
            ]);
            $query->save();
        } 
    }
}
