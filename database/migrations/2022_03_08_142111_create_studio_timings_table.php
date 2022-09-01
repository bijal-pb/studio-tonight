<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStudioTimingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('studio_timings', function (Blueprint $table) {
            $table->id()->index();
            $table->unsignedBigInteger('studio_id')->index()->nullable();
            $table->foreign('studio_id')->references('id')->on('studios')->onDelete('cascade');
            $table->integer('advance_booking')->nullable();
            $table->integer('min_booking')->nullable();
            $table->integer('max_capacity')->nullable();
            $table->boolean('mon')->default(false);
            $table->time('mon_start_time')->nullable();
            $table->time('mon_end_time')->nullable();
            $table->boolean('tue')->default(false);
            $table->time('tue_start_time')->nullable();
            $table->time('tue_end_time')->nullable();
            $table->boolean('wed')->default(false);
            $table->time('wed_start_time')->nullable();
            $table->time('wed_end_time')->nullable();
            $table->boolean('thur')->default(false);
            $table->time('thur_start_time')->nullable();
            $table->time('thur_end_time')->nullable();
            $table->boolean('fri')->default(false);
            $table->time('fri_start_time')->nullable();
            $table->time('fri_end_time')->nullable();
            $table->boolean('sat')->default(false);
            $table->time('sat_start_time')->nullable();
            $table->time('sat_end_time')->nullable();
            $table->boolean('sun')->default(false);
            $table->time('sun_start_time')->nullable();
            $table->time('sun_end_time')->nullable();
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
        Schema::dropIfExists('studio_timings');
    }
}
