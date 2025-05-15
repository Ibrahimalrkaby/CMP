<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCoursePrerequisitesTable extends Migration
{
    public function up()
    {
        Schema::create('course_prerequisites', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('course_id'); 
            $table->unsignedBigInteger('prerequisite_course_id'); 
            $table->timestamps();

            // Foreign keys
            $table->foreign('course_id')->references('id')->on('courses')->onDelete('cascade');
            $table->foreign('prerequisite_course_id')->references('id')->on('courses')->onDelete('cascade');

            // Unique constraint to prevent duplicate entries
            $table->unique(['course_id', 'prerequisite_course_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('course_prerequisites');
    }
}
