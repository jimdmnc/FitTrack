<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attendances', function (Blueprint $table) {
            $table->id(); // Auto-increment Primary Key
            $table->string('rfid_uid'); // Matches `users.rfid_uid`
            $table->date('attendance_date'); // Date column
            $table->timestamps();

            // Foreign Key Constraint
            $table->foreign('rfid_uid')->references('rfid_uid')->on('users')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attendances');
    }
};
