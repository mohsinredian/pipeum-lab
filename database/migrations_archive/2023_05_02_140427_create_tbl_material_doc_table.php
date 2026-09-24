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
        Schema::create('tbl_material_doc', function (Blueprint $table) {
            $table->id();
            $table->string('service_id')->nullable();
            $table->string('previous_work_order')->nullable();
            $table->string('derc_stakeholder_approvals')->nullable();
            $table->string('consumption_details')->nullable();
            $table->string('vend_quatation')->nullable();
            $table->string('photo_product')->nullable();
            $table->string('material_procurement')->nullable();
            $table->string('budget_for_both')->nullable();
            $table->string('others')->nullable();
            $table->string('new_product')->nullable();
            $table->string('cm_rate_ref')->nullable();
            $table->string('vendor_quatation')->nullable();
            $table->string('last_purchase_price')->nullable();
            $table->string('user_estimation')->nullable();
            $table->string('cost_calculation_for_service')->nullable();
            $table->string('created_by')->nullable();
            $table->string('created_at')->nullable();
            $table->string('updated_at')->nullable();
            $table->tinyInteger('status')->default(1);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tbl_material_doc');
    }
};
