<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DropDurationFromAttendances extends Migration
{
    public function up()
    {
        Schema::table('attendances', function (Blueprint $table) {
            $table->dropColumn('duration');
        });
    }

    public function down()
    {
        Schema::table('attendances', function (Blueprint $table) {
            $table->integer('duration')->nullable();
        });
    }
}
