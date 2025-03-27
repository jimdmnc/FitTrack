<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('user_details', function (Blueprint $table) {
            $table->id();
            $table->string('rfid_uid')->unique(); // Foreign key from users table
            $table->integer('age');
            $table->enum('gender', ['Male', 'Female', 'Other']);
            $table->float('weight');
            $table->float('height');
            $table->enum('activity_level', ['Beginner', 'Intermediate', 'Advanced']);
            $table->timestamps();

            // Foreign key relation
            $table->foreign('rfid_uid')->references('rfid_uid')->on('users')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_details');
    }
};
