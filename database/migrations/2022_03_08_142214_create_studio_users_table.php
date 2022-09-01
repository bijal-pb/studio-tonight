<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStudioUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('studio_users', function (Blueprint $table) {
            $table->id()->index();
            $table->unsignedBigInteger('studio_id')->index()->nullable();
            $table->foreign('studio_id')->references('id')->on('studios')->onDelete('cascade');
            $table->string('name');
            $table->string('phone');
            $table->string('email');
            $table->unsignedBigInteger('verification_id')->index()->nullable();
            $table->foreign('verification_id')->references('id')->on('verifications')->onDelete('cascade');
            $table->longText('about')->nullable();
            $table->softDeletes();
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
        Schema::dropIfExists('studio_users');
    }
}
