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
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('lecture_id')->unsigned();
            $table->bigInteger('student_id')->unsigned();
            $table->boolean('present')->default(false);
            $table->timestamps();

            $table->foreign('lecture_id')->references('id')->on('lectures');
            $table->foreign('student_id')->references('student_id')->on('students_data');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendances');
    }
};
