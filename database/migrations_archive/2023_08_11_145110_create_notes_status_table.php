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
        Schema::create('notes_status', function (Blueprint $table) {
            $table->id();
            $table->string('notes_id')->nullable();
            $table->tinyInteger('notes_draft')->default(0);

            $table->string('dep_rew1_id')->nullable();
            $table->tinyInteger('dep_rew1_status')->default(0);
            $table->timestamp('dep_rew1_timestamp')->nullable();
            $table->text('dep_rew1_remark')->nullable();
            $table->string('dep_rew1_action_ip')->nullable();

            $table->string('dep_rew2_id')->nullable();
            $table->tinyInteger('dep_rew2_status')->default(0);
            $table->timestamp('dep_rew2_timestamp')->nullable();
            $table->text('dep_rew2_remark')->nullable();
            $table->string('dep_rew2_action_ip')->nullable();;

            $table->string('dep_rew3_id')->nullable();
            $table->tinyInteger('dep_rew3_status')->default(0);
            $table->timestamp('dep_rew3_timestamp')->nullable();
            $table->text('dep_rew3_remark')->nullable();
            $table->string('dep_rew3_action_ip')->nullable();

            $table->string('dep_rew4_id')->nullable();
            $table->tinyInteger('dep_rew4_status')->default(0);
            $table->timestamp('dep_rew4_timestamp')->nullable();
            $table->text('dep_rew4_remark')->nullable();
            $table->string('dep_rew4_action_ip')->nullable();

            $table->string('hod_id')->nullable();
            $table->tinyInteger('hod_status')->default(0);
            $table->timestamp('hod_timestamp')->nullable();
            $table->text('hod_remark')->nullable();
            $table->string('hod_action_ip')->nullable();
          
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
        Schema::dropIfExists('notes_status');
    }
};
