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
        Schema::create('floor_plan', function (Blueprint $table) {
            $table->id();
            $table->string('company_id')->nullable();;
            $table->string('location_id');
            $table->string('floor_name')->nullable();;
            $table->string('store_room')->nullable();;
            $table->string('gym')->nullable();;
            $table->string('corridor')->nullable();;
            $table->string('cabins')->nullable();;
            $table->string('panel_room')->nullable();;
            $table->string('reception_area')->nullable();;
            $table->string('meeting_room')->nullable();;
            $table->string('ladies_washroom')->nullable();;
            $table->string('gents_washroom')->nullable();;
            $table->string('board_room')->nullable();;
            $table->string('common_area')->nullable();;
            $table->tinyInteger('status')->default(1);
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP'));
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('floor_plan');
    }
};
