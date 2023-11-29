<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = new \App\Models\Profile([
            'username' => 'local102',
            'ecode' => 'local102',
            'email'      => 'jacob@gagroup.net',
            'display_name'  => 'Jacob Calit',
            'first_name'  => 'Jacob',
            'last_name'  => 'Calit',
            'company_id'   => 1,
            'status'  => 'active', // active, disabled, trashed
            'role'       => 'superadmin'
        ]);
        $user->save();
        $dataArr = array(
            array(
                'username' => '100194', 'ecode' => '100194', 'email' => 'jacob@gagroup.net','display_name'  => 'Jacob Calit','first_name'  => 'Jacob',
                'last_name'  => 'Calit','company_id'   => 1,'status'  => 'active', 'role'       => 'superadmin'
            ),
            array(
                'username' => '100264', 'ecode' => '100264', 'email' => 'romel.i@gagroup.net','display_name'  => 'Romel Indemne','first_name'  => 'Romel',
                'last_name'  => 'Indemne','company_id'   => 1,'status'  => 'active', 'role'       => 'superadmin'
            ),
            array(
                'username' => '100672', 'ecode' => '100672', 'email' => 'bernadette.p@grandiose.net','display_name'  => 'Bernadette Puyot','first_name'  => 'Bernadette',
                'last_name'  => 'Puyot','company_id'   => 5,'status'  => 'active', 'role' => 'commercial-manager'
            ),
            array(
                'username' => '500001', 'ecode' => '500001', 'email' => 'joseph.v@grandiose.net','display_name'  => 'Joseph Visaya','first_name'  => 'Joseph',
                'last_name'  => 'Visaya','company_id'   => 5,'status' => 'active', 'role' => 'asset-supervisor'
            ),
            array(
                'username' => '102587', 'ecode' => '102587', 'email' => 'mussaab@gagroup.net','display_name'  => 'Mussaab Aboud','first_name'  => 'Mussaab',
                'last_name'  => 'Aboud','company_id'   => 5,'status' => 'active', 'role' => 'normal'
            ),
            array(
                'username' => '100001', 'ecode' => '100001', 'email' => 'jerico@gagroup.net','display_name'  => 'Jerico','first_name'  => 'Jerico',
                'last_name'  => 'Aboud','company_id'   => 5,'status' => 'active', 'role' => 'normal'
            ),
        );


        foreach($dataArr AS $k => $v){
            $query = new \App\Models\Profile([
                'username'      => $v['username'],
                'ecode'         => $v['ecode'],
                'email'         => $v['email'],
                'display_name'  => $v['display_name'],
                'first_name'    => $v['first_name'],
                'last_name'     => $v['last_name'],
                'company_id'    => $v['company_id'],
                'status'        => $v['status'],
                'role'          => $v['role'],
            ]);
            $query->save();
        }
    }
}
