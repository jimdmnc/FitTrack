<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ModifyUsersTableNullableFields extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            // Make fields nullable except for first_name, last_name, membership_type, and phone_number
            $table->string('gender')->nullable()->change();
            $table->date('start_date')->nullable()->change();
            $table->string('rfid_uid')->nullable()->change();
            $table->date('birthdate')->nullable()->change();
            $table->timestamp('email_verified_at')->nullable()->change();
            $table->string('remember_token', 100)->nullable()->change();
            $table->date('end_date')->nullable()->change();
            
            // Make the password field nullable
            $table->string('password')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            // Ensure there are no NULL values in 'birthdate' before making it non-nullable
            DB::table('users')->whereNull('birthdate')->update(['birthdate' => '2000-01-01']);
    
            // Rollback changes if necessary
            $table->string('gender')->nullable(false)->change();
            $table->date('start_date')->nullable(false)->change();
            $table->string('rfid_uid')->nullable(false)->change();
            $table->date('birthdate')->nullable(false)->change();
            $table->timestamp('email_verified_at')->nullable(false)->change();
            $table->string('remember_token', 100)->nullable(false)->change();
            $table->date('end_date')->nullable(false)->change();
            
            // Rollback the password field to not nullable
            $table->string('password')->nullable(false)->change();
        });
    }
    
}
