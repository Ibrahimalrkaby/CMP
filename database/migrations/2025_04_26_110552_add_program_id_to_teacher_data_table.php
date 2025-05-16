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
        // تحقق من وجود العمود قبل إضافته
        if (!Schema::hasColumn('teacher_data', 'program_id')) {
            Schema::table('teacher_data', function (Blueprint $table) {
                $table->unsignedBigInteger('program_id')->nullable()->after('role');
                $table->foreign('program_id')->references('id')->on('programs')->onDelete('set null');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('teacher_data', function (Blueprint $table) {
            $table->dropForeign(['program_id']); // Drop the foreign key constraint
            $table->dropColumn('program_id');     // Then drop the column
        });
    }
};