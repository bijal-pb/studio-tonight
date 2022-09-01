<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBookingTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('booking_transactions', function (Blueprint $table) {
            $table->id()->index();
            $table->unsignedBigInteger('booking_id')->index();
            $table->foreign('booking_id')->references('id')->on('bookings')->onDelete('cascade');
            $table->string('transaction_id');
            $table->double('amount');
            $table->tinyInteger('status')->comment('1 - success | 2 - declined');
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
        Schema::dropIfExists('booking_transactions');
    }
}
