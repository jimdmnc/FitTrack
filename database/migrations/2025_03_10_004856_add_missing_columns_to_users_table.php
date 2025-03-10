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
        Schema::table('users', function (Blueprint $table) {
            // Only add columns that don't already exist
            if (!Schema::hasColumn('users', 'gender')) {
                $table->string('gender')->after('last_name');
            }
            if (!Schema::hasColumn('users', 'phone_number')) {
                $table->string('phone_number')->after('gender');
            }
            if (!Schema::hasColumn('users', 'membership_type')) {
                $table->string('membership_type')->after('phone_number');
            }
            if (!Schema::hasColumn('users', 'start_date')) {
                $table->date('start_date')->nullable()->after('membership_type');
            }
            if (!Schema::hasColumn('users', 'rfid_uid')) {
                $table->string('rfid_uid')->after('start_date');
            }
        });
    }
    
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            // Drop columns if they exist
            if (Schema::hasColumn('users', 'gender')) {
                $table->dropColumn('gender');
            }
            if (Schema::hasColumn('users', 'phone_number')) {
                $table->dropColumn('phone_number');
            }
            if (Schema::hasColumn('users', 'membership_type')) {
                $table->dropColumn('membership_type');
            }
            if (Schema::hasColumn('users', 'start_date')) {
                $table->dropColumn('start_date');
            }
            if (Schema::hasColumn('users', 'rfid_uid')) {
                $table->dropColumn('rfid_uid');
            }
        });
    }
};
