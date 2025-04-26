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
        Schema::table('teacher_data', function (Blueprint $table) {
            $table->string('role')->after('rank');
        });
    }

    public function down()
    {
        Schema::table('teacher_data', function (Blueprint $table) {
            $table->dropColumn('role');
        });
    }

};
