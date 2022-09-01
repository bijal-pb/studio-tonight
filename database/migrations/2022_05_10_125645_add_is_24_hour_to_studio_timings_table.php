<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIs24HourToStudioTimingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('studio_timings', function (Blueprint $table) {
            $table->boolean('is_24_hour')->after('max_capacity')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('studio_timings', function (Blueprint $table) {
                $table->dropColumn('is_24_hour');
        });
    }
}
