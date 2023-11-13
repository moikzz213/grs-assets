<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class StatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        $dataArr = array(
            array('title'=> 'Good', 'type' => 'condition-type', 'status' => 'active' ),
            array('title'=> 'Maintenance', 'type' => 'incident-type', 'status' => 'active' ),
            array('title'=> 'Damage', 'type' => 'incident-type', 'status' => 'active' ),
            array('title'=> 'In-Used', 'type' => 'asset', 'status' => 'active' ),
            array('title'=> 'In-Stock', 'type' => 'asset', 'status' => 'active' ),
            array('title'=> 'Perfect', 'type' => 'condition-type', 'status' => 'active' ),
            array('title'=> 'Pending', 'type' => 'incident', 'status' => 'active' ),
            array('title'=> 'Completed', 'type' => 'incident', 'status' => 'active' ),
            array('title'=> 'onProcess', 'type' => 'incident', 'status' => 'active' ),
            array('title'=> 'Vendor', 'type' => 'incident', 'status' => 'active' ),
        );

        foreach($dataArr AS $k => $v){ 
            $query = new \App\Models\Status([
                'title' => $v['title'],
                'type'  => $v['type'],
                'status'  => $v['status'],
            ]);
            $query->save();
        } 
    }
}
