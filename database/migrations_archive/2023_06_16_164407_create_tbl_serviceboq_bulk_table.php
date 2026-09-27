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
        Schema::table('tbl_serviceboq_bulk', function (Blueprint $table) {
            $table->id();
            $table->string('nv_id')->nullable();
            $table->string('service_code')->nullable();
            $table->string('uom')->nullable();
            $table->string('description')->nullable();
            $table->string('rate')->nullable();
            $table->string('quantity')->nullable();
            $table->string('amount')->nullable();
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
        Schema::table('tbl_serviceboq_bulk', function (Blueprint $table) {
            //
        });
    }
};
