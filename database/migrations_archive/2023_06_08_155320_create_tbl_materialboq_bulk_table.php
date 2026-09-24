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
        Schema::create('tbl_materialboq_bulk', function (Blueprint $table) {
            $table->id();
            $table->string('material_id')->nullable();
            $table->string('nv_id')->nullable();
            $table->string('material_code')->nullable();
            $table->string('uom')->nullable();
            $table->string('material_short_text')->nullable();
            $table->string('rate')->nullable();
            $table->string('quantity')->nullable();
            $table->string('amount')->nullable();
            // $table->unsignedInteger('created_at');
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
        Schema::dropIfExists('tbl_materialboq_bulk');
    }
};
