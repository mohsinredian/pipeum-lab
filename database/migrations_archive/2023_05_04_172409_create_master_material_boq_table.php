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
        Schema::create('master_material_boq', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_id');
             $table->string('activity')->nullable();
             $table->string('uom')->nullable();
             $table->string('material_short_text');
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
        Schema::dropIfExists('master_material_boq');
    }
};
