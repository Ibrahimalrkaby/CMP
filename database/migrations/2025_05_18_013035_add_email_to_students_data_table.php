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
        Schema::table('students_data', function (Blueprint $table) {
            $table->string('email')->unique()->after('full_name'); 
        });
    }

    public function down()
    {
        Schema::table('students_data', function (Blueprint $table) {
            $table->dropColumn('email');
        });
    }

};
