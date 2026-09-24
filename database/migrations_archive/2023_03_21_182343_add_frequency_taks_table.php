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
        Schema::table('taks', function (Blueprint $table) {
            $table->string('d1')->nullable();
            $table->string('d2')->nullable();
            $table->string('d3')->nullable();
            $table->string('d4')->nullable();
            $table->string('d5')->nullable();
            $table->string('d6')->nullable();
            $table->string('d7')->nullable();
            $table->string('m1')->nullable();
            $table->string('m2')->nullable();
            $table->string('m3')->nullable();
            $table->string('m4')->nullable();
            $table->string('m5')->nullable();
            $table->string('m6')->nullable();
            $table->string('m7')->nullable();
            $table->string('m8')->nullable();
            $table->string('m9')->nullable();
            $table->string('m10')->nullable();
            $table->string('m11')->nullable();
            $table->string('m12')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('taks', function (Blueprint $table) {
            //
        });
    }
};
