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
<<<<<<< HEAD
            $table->unsignedBigInteger('lecture_id');
            $table->unsignedBigInteger('student_id');
=======
            $table->bigInteger('lecture_id')->unsigned();
            $table->bigInteger('student_id')->unsigned();
>>>>>>> da85b30997a9f549c26d237af080612837864fda
            $table->boolean('present')->default(false);
            $table->timestamps();

            $table->foreign('lecture_id')->references('id')->on('lectures');
            $table->foreign('student_id')->references('student_id')->on('students_data');
<<<<<<< HEAD
});

=======
        });
>>>>>>> da85b30997a9f549c26d237af080612837864fda
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendances');
    }
};
