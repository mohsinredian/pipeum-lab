<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Subsidy;

class SubsidySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Subsidy::Factory()->count(100)->create();
    }
}
