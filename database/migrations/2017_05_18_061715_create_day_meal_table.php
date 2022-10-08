<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDayMealTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('day_meal', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->integer('day_id');
            $table->integer('meal_id')->nullable();
            $table->integer('day_slot_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('day_meal');
    }
}
