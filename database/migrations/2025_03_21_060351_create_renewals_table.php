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
        Schema::create('renewals', function (Blueprint $table) {
            $table->id();
            $table->string('rfid_uid'); // No unique constraint here
            $table->string('membership_type');
            $table->date('start_date');
            $table->date('end_date');
            $table->timestamps();
    
            // Foreign key to users table
            $table->foreign('rfid_uid')->references('rfid_uid')->on('users')->onDelete('cascade');
        });
    }
    
    public function down()
    {
        Schema::dropIfExists('renewals');
    }
    
};
