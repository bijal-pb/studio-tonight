<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTypeFieldToBookingTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('booking_transactions', function (Blueprint $table) {
            $table->boolean('type')->after('status')->default('0')->comment('0 - customer | 1 -studio');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('booking_transactions', function (Blueprint $table) {
            $table->dropColumn('type');
        });
    }
}
