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
        Schema::create('students_data', function (Blueprint $table) {
            $table->id();
            $table->string('phone');
            $table->string('department');

            $table->unsignedBigInteger('student_id');
            $table->foreign('student_id')->references('id')->on('users')->onDelete('cascade');

            $table->bigInteger('personal_id')->unsigned();
            $table->foreign('personal_id')->references('id')->on('students_personal_date');

            $table->string('guardian_id');
            $table->foreign('guardian_id')->references('national_id')->on('students_guardian');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('students_data'); // Already exists here
    }
};
