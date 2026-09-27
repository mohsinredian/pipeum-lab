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
        Schema::create('clarification_log', function (Blueprint $table) {
        $table->id();
        $table->bigInteger('nv_id')->nullable();
        $table->bigInteger('service_id')->nullable();
        $table->bigInteger('material_id')->nullable();
        $table->bigInteger('user_id')->nullable();
        $table->text('user_name')->nullable();
        $table->longtext('clarification_remark')->nullable();
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
        Schema::dropIfExists('clarification_log');
    }
};
