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
        Schema::create('grades', function (Blueprint $table) {
            $table->id();

            // Student relationship
            $table->unsignedBigInteger('student_id');
            $table->foreign('student_id')
                ->references('id')
                ->on('students_data')
                ->onDelete('cascade');

            // Course relationship
            $table->unsignedBigInteger('course_id');
            $table->foreign('course_id')
                ->references('id')
                ->on('course_registrations')
                ->onDelete('cascade');


            // Grade columns
            $table->decimal('midterm_exam', 5, 2)->nullable();
            $table->decimal('practical_exam', 5, 2)->nullable();
            $table->decimal('oral_exam', 5, 2)->nullable();
            $table->decimal('year_work', 5, 2)->nullable();
            $table->decimal('final_grade', 5, 2)->nullable();
            $table->decimal('total', 5, 2)->nullable();

            $table->decimal('course_grade', 5, 2)->nullable();

            $table->timestamps();

            // Unique combination constraint
            $table->unique(['student_id', 'course_id']);
        });
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('grades');
    }
};
