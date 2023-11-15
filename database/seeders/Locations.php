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

        $dataArr = array(
            array('title'=> 'GRS - Dubai Sports City - Victory Heights', 'code' => 'DBVHS'),
            array('title'=> 'GRS - Jumeirah 2', 'code' => 'DBJ2RS'),
            array('title'=> 'GRS - Al Qanna', 'code' => 'ADALQ'),
            array('title'=> 'GRS - Al Sahel Towers', 'code' => 'ADAST'),
            array('title'=> 'GRS - Horizon Tower', 'code' => 'ADHZT'),
            array('title'=> 'GRS - Al Masdar City', 'code' => 'ADMSD'),
            array('title'=> 'GRS - Al Shafar', 'code' => 'DBALS'),
            array('title'=> 'GRS - Al Sayyah', 'code' => 'DBASR'),
            array('title'=> 'GRS - Al Barsha', 'code' => 'DBBAR'),
            array('title'=> 'GRS - Kaizen Tower', 'code' => 'DBKZT'),
            array('title'=> 'GRS - Marina', 'code' => 'DBMAR'),
            array('title'=> 'GRS - Meena Al Wasl', 'code' => 'DBMAW'),
            array('title'=> 'GRS - Murjan', 'code' => 'DBMRJ'),
            array('title'=> 'GRS - DIP', 'code' => 'DBOCM'),
            array('title'=> 'GRS - Silicon Store', 'code' => 'DBSLO'),
            array('title'=> 'GRS - Dubai Sports City', 'code' => 'DBSPC'),
            array('title'=> 'GRS - Business Bay', 'code' => 'DBBLV'),
            array('title'=> 'GRS - Avenue Mall', 'code' => 'DBMAV'),
            array('title'=> 'GRS - Hoor 17', 'code' => 'DBHR17'),
            array('title'=> 'GRS - JVC Binghatti Rose', 'code' => 'DBBGR'),
            array('title'=> 'GRS - C12', 'code' => 'ADC12'),
            array('title'=> 'GRS - C11 Mall', 'code' => 'ADC11'),
        );

        foreach($dataArr AS $k => $v){ 
            $query = new \App\Models\Location([
                'title' => $v['title'],
                'code'  => $v['code']                
            ]);
            $query->save();
        } 
       
    }
}
