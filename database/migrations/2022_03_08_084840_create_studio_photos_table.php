<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStudioPhotosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('studio_photos', function (Blueprint $table) {
            $table->id()->index();
            $table->unsignedBigInteger('studio_id')->index()->nullable();
            $table->foreign('studio_id')->references('id')->on('studios')->onDelete('cascade');
            $table->longText('photo');
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
        Schema::dropIfExists('studio_photos');
    }
}
