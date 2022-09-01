<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddNameToBookingServicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('booking_services', function (Blueprint $table) {
            $table->string('name')->after('studio_service_id');
            $table->double('fees',8,2)->after('name');
            $table->longText('description')->nulable()->after('fees');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('booking_services', function (Blueprint $table) {
            $table->dropColumn('name');
            $table->dropColumn('fees');
            $table->dropColumn('description');
        });
    }
}
