<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RemoveOnUpdateFromTimeInInAttendancesTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        // Temporarily disable strict mode for this migration
        DB::statement('SET SESSION sql_mode = "";');

        Schema::table('attendances', function (Blueprint $table) {
            // Modify the time_in column to remove ON UPDATE CURRENT_TIMESTAMP
            $table->timestamp('time_in')->default(DB::raw('CURRENT_TIMESTAMP'))->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('attendances', function (Blueprint $table) {
            // Revert the change (optional, if you want to rollback)
            $table->timestamp('time_in')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'))->change();
        });
    }
}