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
        Schema::create('mailbox', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('sender_id');
            $table->enum('sender_type', ['admin', 'doctor', 'assistant']);
            $table->unsignedBigInteger('student_id');
            $table->string('subject');
            $table->text('message');
            $table->boolean('is_read')->default(false);
            $table->timestamps();
        
            $table->foreign('student_id')->references('id')->on('students_data')->onDelete('cascade');
        });
        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mailbox');
    }
};
