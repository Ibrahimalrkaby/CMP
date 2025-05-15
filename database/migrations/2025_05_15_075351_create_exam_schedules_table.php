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
        Schema::create('exam_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained()->onDelete('cascade');
            $table->string('exam_type'); // final, midterm
            $table->date('exam_date');
            $table->time('start_time');
            $table->time('end_time');
            $table->string('location');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('exam_schedules');
    }
};
