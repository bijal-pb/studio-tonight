<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStudioDocumentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('studio_documents', function (Blueprint $table) {
            $table->id()->index();
            $table->unsignedBigInteger('studio_id')->index()->nullable();
            $table->foreign('studio_id')->references('id')->on('studios')->onDelete('cascade');
            $table->unsignedBigInteger('studio_user_id')->index()->nullable();
            $table->foreign('studio_user_id')->references('id')->on('studio_users')->onDelete('cascade');
            $table->longText('image');
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
        Schema::dropIfExists('studio_documents');
    }
}
