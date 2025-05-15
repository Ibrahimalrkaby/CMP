<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCourseSchedulesTable extends Migration
{
    public function up(): void
    {
        Schema::create('course_schedules', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('course_id');
            $table->unsignedBigInteger('teacher_id')->nullable();
            $table->string('day'); 
            $table->time('start_time');
            $table->time('end_time');
            $table->string('location')->nullable(); 
            $table->enum('type', ['lecture', 'section', 'lab'])->default('lecture');

            $table->timestamps();

            $table->foreign('course_id')->references('id')->on('courses')->onDelete('cascade');
            $table->foreign('teacher_id')->references('id')->on('teacher_data')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('course_schedules');
    }
};