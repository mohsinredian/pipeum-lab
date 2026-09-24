<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use DB;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('roles')->insert([
            'name' => 'Admin',
            'status' => '1',
        ]);
        DB::table('roles')->insert([
            'name' => 'POC',
            'status' => '1',
        ]);
    }
}
