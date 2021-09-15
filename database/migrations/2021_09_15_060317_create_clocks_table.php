<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateClocksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('clocks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id');
            $table->time('clock_in');
            $table->time('clock_out')->nullable();
            $table->decimal('clock_in_lat', $precision = 8, $scale = 6);
            $table->decimal('clock_in_long', $precision = 9, $scale = 6);
            $table->decimal('clock_out_lat', $precision = 8, $scale = 6)->nullable();
            $table->decimal('clock_out_long', $precision = 9, $scale = 6)->nullable();
            $table->time('total_working_hours')->nullable();
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
        Schema::dropIfExists('clocks');
    }
}
