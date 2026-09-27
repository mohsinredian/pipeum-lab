<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use DB;
use Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->insert([
            'name' => 'B N Manish',
            'email' =>  'admin@gmail.com',
            'email_verified_at' =>  NULL,
            'password'  =>  Hash::make('12345'),
            'mobile_no'  =>  '8116648011',
            'role_id'  =>  '1',
            'remember_token'  =>  NULL,
            'status' => '1',
        ]);

        DB::table('users')->insert([
            'name' => 'Priyanshu',
            'email' =>  'poc@gmail.com',
            'email_verified_at' =>  NULL,
            'password'  =>  Hash::make('12345'),
            'mobile_no'  =>  '1234567890',
            'role_id'  =>  '2',
            'remember_token'  =>  NULL,
            'status' => '1',
        ]);
    }
}
