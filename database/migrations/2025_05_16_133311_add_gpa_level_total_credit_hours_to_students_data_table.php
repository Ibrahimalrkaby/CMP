<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('students_data', function (Blueprint $table) {
            $table->decimal('gpa', 3, 2)->nullable(); // GPA, e.g., 3.75
            $table->integer('level')->nullable(); // Academic level/year
            $table->integer('total_credit_hours')->default(0); // Total credit hours completed
        });
    }

    public function down(): void
    {
        Schema::table('students_data', function (Blueprint $table) {
            $table->dropColumn(['gpa', 'level', 'total_credit_hours']);
        });
    }
};
