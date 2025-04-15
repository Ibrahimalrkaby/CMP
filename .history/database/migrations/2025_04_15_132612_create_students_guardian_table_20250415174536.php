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
        Schema::create('students_guardian', function (Blueprint $table) {
            $table->id();
            $table->string('national_id')->unique();
            $table->string('email')->unique();
            $table->string('phone');
            $table->string('city');
            $table->timestamps();
        });
        //
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('students_guardian'); // Add this
    }
};
