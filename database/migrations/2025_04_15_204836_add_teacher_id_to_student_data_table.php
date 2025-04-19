<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('students_data', function (Blueprint $table) {
            // Add nullable supervisor_id column
            $table->unsignedBigInteger('supervisor_id')->nullable()->after('guardian_id');

            // Add foreign key constraint
            $table->foreign('supervisor_id')
                ->references('id')
                ->on('teacher_data') // Replace 'teacher_data' with your actual table name
                ->onDelete('set null'); // Handle supervisor deletion
        });
    }

    public function down(): void
    {
        Schema::table('students_data', function (Blueprint $table) {
            $table->dropForeign(['supervisor_id']);
            $table->dropColumn('supervisor_id');
        });
    }
};
