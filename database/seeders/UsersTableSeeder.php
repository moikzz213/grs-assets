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
            'username' => 'local101',
            'ecode' => 'local101',
            'email'      => 'jacob@gagroup.net',
            'display_name'  => 'Jacob Calit',
            'first_name'  => 'Jacob',
            'last_name'  => 'Calit',
            'company_id'   => 1,
            'status'     => 'active', // active, disabled, trashed
            'role'       => 'superadmin'
        ]);
        $user->save();


    }
}
