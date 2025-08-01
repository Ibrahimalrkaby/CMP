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
        if (!Schema::hasColumn('course_student', 'grade')) {
            Schema::table('course_student', function (Blueprint $table) {
                $table->string('grade')->nullable()->after('student_id');
            });
        }
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('course_student', function (Blueprint $table) {
            $table->dropColumn('grade');
        });
    }
};
