<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->string('email')->nullable();
            $table->string('password')->nullable();
            $table->longText('avatar')->nullable();
            $table->tinyInteger('type')->comment('1-studio | 2 - customer');
            $table->string('social_type')->comment('google | mac | facebook')->nullable();
            $table->string('social_id')->unique();
            $table->string('lat')->nullable();
            $table->string('lang')->nullable();
            $table->text('address')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('country')->nullable();
            $table->boolean('is_notification')->comment('1 - enable | 0 - disable')->default(1);
            $table->boolean('is_approve')->comment('1 - active | 0 - deacvtive')->default(0);
            $table->boolean('status')->comment('1 - active | 0 - deactive')->default(1);
            $table->string('device_type')->nullable();
            $table->text('device_token')->nullable();
            $table->string('stripe_id')->nullable();
            $table->text('firebase_id')->nullable();
            $table->rememberToken();
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
        Schema::dropIfExists('users');
    }
}
