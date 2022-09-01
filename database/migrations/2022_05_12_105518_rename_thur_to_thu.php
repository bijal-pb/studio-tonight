<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RenameThurToThu extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('studio_timings', function (Blueprint $table) {
            $table->renameColumn('thur', 'thu');
            $table->renameColumn('thur_start_time', 'thu_start_time');
            $table->renameColumn('thur_end_time', 'thu_end_time');
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
            $table->renameColumn('thu', 'thur');
            $table->renameColumn('thu_start_time', 'thur_start_time');
            $table->renameColumn('thu_end_time', 'thur_end_time');
        });
    }
}
