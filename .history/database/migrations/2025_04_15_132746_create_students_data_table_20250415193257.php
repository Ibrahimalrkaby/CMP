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
            $table->string('name');
            $table->string('email')->unique();
            $table->string('phone');
            $table->string('department');

            // For personal_id (assuming it references `students_personal_date.id`):
            $table->bigInteger('personal_id')->unsigned();
            $table->foreign('personal_id')->references('id')->on('students_personal_date');

            // Fix for guardian_id (references `students_guardian.national_id`):
            $table->string('guardian_id'); // Matches the type of `national_id` (string)
            $table->foreign('guardian_id')->references('national_id')->on('students_guardian'); // Corrected

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
