<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->string('rfid_uid'); // Stores RFID UID (acts as user_id)
            $table->timestamp('time_in'); // Check-in time
            $table->timestamp('time_out')->nullable(); // Check-out time
            $table->date('date'); // Stores the date of attendance
            $table->timestamps();

            // Foreign key constraint linking to users table
            $table->foreign('rfid_uid')->references('rfid_uid')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendances');
    }
};
