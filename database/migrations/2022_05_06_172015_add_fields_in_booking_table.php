<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFieldsInBookingTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->string('booking_id')->unique()->after('id');
            $table->double('total_hour',8,2)->after('end_time')->nullabel();
            $table->boolean('status')->default(0)->comment('0 - in progress | 1 - success')->after('amount');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn('booking_id');
            $table->dropColumn('total_hour');
            $table->dropColumn('status');
        });
    }
}
