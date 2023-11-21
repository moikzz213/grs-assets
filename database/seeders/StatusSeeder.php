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

        $dataArr2 = array(
            array('title'=> 'Asset Request (as per approved new/renovation layout)', 'type' => 'request', 'status' => 'active' ),
            array('title'=> 'Asset Request (to replaced damaged asset)', 'type' => 'request', 'status' => 'active' ),
            array('title'=> 'Asset Request (additional asset not reflected in the approved layout)', 'type' => 'request', 'status' => 'active' ),
            array('title'=> 'Asset Transfer (Removal of equipment not reflected in the approved layout)', 'type' => 'transfer', 'status' => 'active' ),
            array('title'=> 'Asset Transfer (Removal of equipment as per approved renovation layout)', 'type' => 'transfer', 'status' => 'active' ), 
        );

        foreach($dataArr2 AS $k => $v){ 
            $query = new \App\Models\ApprovalSetup([
                'title' => $v['title'],
                'type'  => $v['type'],
                'status'  => $v['status'],
            ]);
            $query->save();
        }  
        
    }
}
