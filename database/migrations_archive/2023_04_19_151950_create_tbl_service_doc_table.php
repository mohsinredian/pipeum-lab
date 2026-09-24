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
        Schema::create('tbl_service_doc', function (Blueprint $table) {
            $table->id();
            $table->string('service_id')->nullable();
            $table->string('cost_calculation_for_service')->nullable();
            $table->string('copy_of_previous_work')->nullable();
            $table->string('copy_of_derc_other')->nullable();
            $table->string('consuption_details')->nullable();
            $table->string('buget_stmt_for_both')->nullable();
            $table->string('photographs_of_product')->nullable();
            $table->string('material_procurement')->nullable();
            $table->string('vendor_quatation')->nullable();
            $table->string('others')->nullable();
            $table->string('created_by')->nullable();
            $table->string('created_at')->nullable();
            $table->string('updated_at')->nullable();
            $table->tinyInteger('status')->default(1);

            // $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tbl_service_doc');
    }
};
