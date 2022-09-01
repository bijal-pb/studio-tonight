<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class NewTableSocialLogins extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('social_profile', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('user_id')->unsigned();
            $table->string('platform_id')->nullable();
            $table->longText('url')->nullable();
            $table->longText('avatar')->nullable();
            $table->string('platform')->default('google');
        });

        Schema::table('social_profile', function (Blueprint $table) {
            $table->foreign('user_id')->references('id')->on('users')->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('social_profile');
    }
}
