<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use DB;

class DivisionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('divisions')->truncate();
        $data  = array(
            ['division' => 'CHANDNI CHOWK','company' => 'BYPL','status' => '1'],
            ['division' => 'DARYAGANJ','company' => 'BYPL','status' => '1'],
            ['division' => 'PAHARGANJ','company' => 'BYPL','status' => '1'],
            ['division' => 'SHANKAR ROAD','company' => 'BYPL','status' => '1'],
            ['division' => 'PATEL NAGAR','company' => 'BYPL','status' => '1'],
            ['division' => 'JHILMIL','company' => 'BYPL','status' => '1'],
            ['division' => 'KRISHNA NAGAR','company' => 'BYPL','status' => '1'],
            ['division' => 'LAKSHMI NAGAR','company' => 'BYPL','status' => '1'],
            ['division' => 'MAYUR VIHAR','company' => 'BYPL','status' => '1'],
            ['division' => 'VASUNDRA ENCLAVE','company' => 'BYPL','status' => '1'],
            ['division' => 'DILSHAD GARDEN','company' => 'BYPL','status' => '1'],
            ['division' => 'YAMUNA VIHAR','company' => 'BYPL','status' => '1'],
            ['division' => 'KARAWAL NAGAR','company' => 'BYPL','status' => '1'],
            ['division' => 'NANAD NAGARI','company' => 'BYPL','status' => '1'],
            ['division' => 'ALAKNANDA','company' => 'BRPL','status' => '1'],
            ['division' => 'KHANPUR','company' => 'BRPL','status' => '1'],
            ['division' => 'SAKET','company' => 'BRPL','status' => '1'],
            ['division' => 'VANSANT KUNJ','company' => 'BRPL','status' => '1'],
            ['division' => 'NEHRU PLACE','company' => 'BRPL','status' => '1'],
            ['division' => 'NIZAMUDDIN','company' => 'BRPL','status' => '1'],
            ['division' => 'SARITA VIHAR','company' => 'BRPL','status' => '1'],
            ['division' => 'NEW FRIENDS COLONY','company' => 'BRPL','status' => '1'],
            ['division' => 'RK PURAM','company' => 'BRPL','status' => '1'],
            ['division' => 'HAUZ JHAS','company' => 'BRPL','status' => '1'],
            ['division' => 'JANAKPURI','company' => 'BRPL','status' => '1'],
            ['division' => 'NAJAFGARH','company' => 'BRPL','status' => '1'],
            ['division' => 'JAFFARPUR','company' => 'BRPL','status' => '1'],
            ['division' => 'NANGLOI','company' => 'BRPL','status' => '1'],
            ['division' => 'MUNDKA','company' => 'BRPL','status' => '1'],
            ['division' => 'PUNJABI BAGH','company' => 'BRPL','status' => '1'],
            ['division' => 'TAGORE GARDEN','company' => 'BRPL','status' => '1'],
            ['division' => 'VIKASPURI','company' => 'BRPL','status' => '1'],
            ['division' => 'UTTAM NAGAR','company' => 'BRPL','status' => '1'],
            ['division' => 'MOHAN GARDEN','company' => 'BRPL','status' => '1'],
            ['division' => 'PALAM','company' => 'BRPL','status' => '1'],
            ['division' => 'DWARKA','company' => 'BRPL','status' => '1'],
        );

        DB::table('divisions')->insert($data);
    }
}