<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('weight_logs', function (Blueprint $table) {
            $table->id();
            $table->string('rfid_uid', 50); // Changed to string to match your RFID format
            $table->decimal('weight', 8, 2); // Using decimal instead of float for precision
            $table->date('log_date');
            $table->timestamps();

            // Correct foreign key - references rfid_uid in user_details
            $table->foreign('rfid_uid')
                  ->references('rfid_uid')
                  ->on('user_details')
                  ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('weight_logs');
    }
};