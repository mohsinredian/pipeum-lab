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
        Schema::table('nvservicestatus', function (Blueprint $table) {
            $table->text('hod_remark')->nullable();
            $table->text('cpmg_remark')->nullable();
            $table->text('ces_remark')->nullable();
            $table->text('bt_remark')->nullable();
            $table->text('ceo_nominee_remark')->nullable();
            $table->text('ceo_remark')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('nvservicestatus', function (Blueprint $table) {
            //
        });
    }
};
