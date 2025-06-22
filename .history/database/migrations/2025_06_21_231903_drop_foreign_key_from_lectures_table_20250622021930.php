<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('lectures', function (Blueprint $table) {
            // Drop the foreign key constraint first
            $table->dropForeign(['student_id']);

            // Then drop the column
            $table->dropColumn('student_id');
        });
    }

    public function down()
    {
        Schema::table('lectures', function (Blueprint $table) {
            // Recreate the column
            $table->bigInteger('student_id')->unsigned()->nullable();

            // Recreate the foreign key (adjust if needed)
            $table->foreign('student_id')->references('id')->on('student_data');
        });
    }
};
