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
        Schema::create('tble_material_stages', function (Blueprint $table) {
            $table->id();
            $table->string('dept_id');
            $table->string('nv_id')->nullable();
            $table->string('user_id')->nullable();
            $table->string('dop')->nullable();
            $table->longText('proposal_name')->nullable();
            $table->longText('background')->nullable();
            $table->longText('just_Prop')->nullable();
            $table->string('cost_trend_year1')->nullable();
            $table->string('cost_trend_year2')->nullable();
            $table->string('cost_trend_year3')->nullable();
            $table->longText('benefit')->nullable();
            $table->date('imp_to')->nullable();
            $table->date('imp_from')->nullable();
            $table->longText('imp_plan')->nullable();
            $table->string('prop_type')->nullable();
            $table->string('worktype')->nullable();
            $table->string('scheme_no')->nullable();
            $table->longText('scheme_des')->nullable();
            $table->string('scheme_type')->nullable();
            $table->string('derc_ref_no')->nullable();
            $table->string('derc_approval')->nullable();
            $table->date('derc_app_date')->nullable();
            $table->string('material_code')->nullable();
            $table->longText('mat_des')->nullable();
            $table->longText('mat_just')->nullable();
            $table->string('mat_group')->nullable();
            $table->string('uom')->nullable();
            $table->string('rate')->nullable();
            $table->string('quantity')->nullable();
            $table->string('total_amount')->nullable();
            $table->string('delivery_schedule')->nullable();

            $table->string('service_code')->nullable();
            $table->longText('ser_des')->nullable();
            $table->string('ser_uom')->nullable();
            $table->string('ser_rate')->nullable();
            $table->string('ser_quantity')->nullable();
            $table->string('ser_total_amount')->nullable();
            $table->string('ser_rate_ref')->nullable();


            $table->string('budget_avl')->nullable();
            $table->string('ptr_mva')->nullable();
            $table->string('dt_mva')->nullable();
            $table->string('ehv_line')->nullable();
            $table->string('ht_line')->nullable();
            $table->string('lt_line')->nullable();
            $table->longText('root_cause_analysis')->nullable();
            $table->longText('cause_analysis')->nullable();
            $table->longText('special_remarks')->nullable();
            $table->decimal('total_budget_material',5,2)->nullable();
            $table->string('prop_number')->nullable();
            $table->string('mode_award')->nullable();
            $table->string('past_3_year_actual_cost_fy')->nullable();
            $table->string('past_3_year_actual_cost')->nullable();
            $table->string('past_3_year_actual_cost_service')->nullable();
            $table->string('past_practice_follow')->nullable();
            $table->date('amc_prop_start_date')->nullable();
            $table->date('amc_prop_end_date')->nullable();
            $table->string('estimate_amount_of_service')->nullable();
            $table->string('estimate_amount_of_service_civil')->nullable();
            $table->string('estimate_amount_of_rr_charge')->nullable();
            $table->string('estimate_amount_other')->nullable();
            $table->decimal('total_budget_service', 5, 2)->nullable();
            $table->decimal('total_budget_both', 5, 2)->nullable();
            $table->string('cap_add')->nullable();
            $table->string('ser_rel_nv')->nullable();
           
            
            $table->string('material_id')->nullable();
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
           
            $table->tinyInteger('status')->default(1);
            $table->string('created_at')->nullable();
            $table->string('updated_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tble_material_stages');
    }
};
