<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIsActiveToStudiosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('studios', function (Blueprint $table) {
            $table->boolean('is_active')->after('area')->default(true)->comment('0 -deactive  | 1 -active');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('studios', function (Blueprint $table) {
            $table->dropColumn('is_active');
        });
    }
}
