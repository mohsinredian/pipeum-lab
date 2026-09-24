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
        Schema::create('opex_workflows', function (Blueprint $table) {
            $table->id();
            $table->text('work_dep')->nullable();
            $table->text('work_rew1')->nullable();
            $table->text('work_rew2')->nullable();
            $table->text('work_rew3')->nullable();
            $table->text('work_rew4')->nullable();
            $table->text('approver')->nullable();
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
        Schema::dropIfExists('opex_workflows');
    }
};
