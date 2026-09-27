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
        Schema::create('nvmaterialstatus', function (Blueprint $table) {
            $table->id();
            $table->string('service_id')->nullable();
            $table->string('hod_id')->nullable();
            $table->tinyInteger('hod_status')->default(0);
            $table->timestamp('hod_timestamp')->nullable();
            $table->string('cpmg_id')->nullable();
            $table->tinyInteger('cpmg_status')->default(0);
            $table->timestamp('cpmg_timestamp')->nullable();
            $table->string('bt_id')->nullable();
            $table->tinyInteger('bt_status')->default(0);
            $table->timestamp('bt_timestamp')->nullable();
            $table->string('ceo_nominee_id')->nullable();
            $table->tinyInteger('ceo_nominee_status')->default(0);
            $table->timestamp('ceo_nominee_timestamp')->nullable();
            $table->string('ceo_id')->nullable();
            $table->tinyInteger('ceo_status')->default(0);
            $table->timestamp('ceo_timestamp')->nullable();
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
        Schema::dropIfExists('nvmaterialstatus');
    }
};
