<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('capex_master', function (Blueprint $table) {
            $table->double('capx_fy_one',9,3)->change();
            $table->double('capx_fy_two',9,3)->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('capex_master', function (Blueprint $table) {
            //
        });
    }
};
