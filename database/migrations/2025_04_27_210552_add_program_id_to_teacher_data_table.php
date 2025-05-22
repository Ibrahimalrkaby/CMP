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
                $table->bigInteger('program_id')->unsigned()->after('role');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('teacher_data', function (Blueprint $table) {
            $table->dropColumn('program_id');
        });
    }
};
