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
        Schema::create('tbl_service', function (Blueprint $table) {
            $table->id();
            $table->string('dept_id');
            $table->string('nv_id')->nullable();
            $table->string('user_id')->nullable();
            $table->string('dop_ref_no')->nullable();
            $table->longText('proposal_name')->nullable();
            $table->longText('background')->nullable();
            $table->longText('just_of_proposal')->nullable();
            // $table->string('past_3_year_actual_cost_fy')->nullable();
            // $table->string('past_3_year_actual_cost')->nullable();
            // $table->string('past_3_year_actual_cost_service')->nullable();
            $table->longText('benefit')->nullable();
            $table->string('implementation_period_from')->nullable();
            $table->string('implementation_period_to')->nullable();
            $table->longText('implementation_plan_year_wise')->nullable();
            $table->string('type_of_proposal')->nullable();
            $table->string('mode_of_award_of_service')->nullable();
            $table->string('amc_proposal_sdate')->nullable();
            $table->string('amc_proposal_edate')->nullable();
            $table->string('budget_available')->nullable();
            $table->string('estimate_amount_of_service')->nullable();
            $table->string('estimate_amount_of_service_civil')->nullable();
            $table->string('estimate_amount_of_rr_chnage')->nullable();
            $table->string('estimate_amount_other')->nullable();
            // $table->string('cost_calculation_for_service')->nullable();
            $table->decimal('total_buget', 5, 2)->nullable();
            // $table->string('copy_of_previous_work')->nullable();
            // $table->string('copy_of_derc_other')->nullable();
            // $table->string('consuption_details')->nullable();
            // $table->string('buget_stmt_for_both')->nullable();
            // $table->string('photographs_of_product')->nullable();
            // $table->string('material_procurement')->nullable();
            // $table->string('vendor_quatation')->nullable();
            // $table->string('others')->nullable();
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
        Schema::dropIfExists('tbl_service');
    }
};
