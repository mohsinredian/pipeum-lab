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
        Schema::create('capex_master', function (Blueprint $table){
            $table->id();
            $table->string('head');
            $table->string('sub_head');
            $table->string('brp_head');
            $table->string('status');
            $table->bigInteger('capx_fy_one');
            $table->bigInteger('capx_fy_two');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
};
