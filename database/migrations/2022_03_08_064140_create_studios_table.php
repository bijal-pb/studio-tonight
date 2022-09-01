<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStudiosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('studios', function (Blueprint $table) {
            $table->id()->index();
            $table->string('title');
            $table->unsignedBigInteger('user_id')->index()->nullable();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->unsignedBigInteger('type_id')->index()->nullable();
            $table->foreign('type_id')->references('id')->on('types')->onDelete('cascade');
            $table->double('price',8,2);
            $table->longText('description')->nullable();
            $table->longText('rules')->nullable();
            $table->longText('cancel_policy')->nullable();
            $table->longText('refund_policy')->nullable();
            $table->longText('availability')->nullable();
            $table->string('lat')->nullable();
            $table->string('lang')->nullable();
            $table->string('address')->nullable();
            $table->string('city')->index()->nullable();
            $table->string('state')->nullable();
            $table->string('country')->nullable();
            $table->string('area')->nullable();
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
        Schema::dropIfExists('studios');
    }
}
