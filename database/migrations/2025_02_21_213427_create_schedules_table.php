<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('schedules', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('movie_id')->comment('列');
            $table->foreign('movie_id')->references('id')->on('movies');
            $table->dateTime('start_time')->comment('上映開始時刻');
            $table->dateTime('end_time')->comment('上映終了時刻');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // drop fk
        Schema::table('schedules', function (Blueprint $table) {
            $table->dropForeign(['movie_id']);
            $table->dropColumn('movie_id');
        });

        Schema::dropIfExists('schedules');
    }
    
};
