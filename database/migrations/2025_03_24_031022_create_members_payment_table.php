<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('members_payment', function (Blueprint $table) {
            $table->id();
            $table->string('rfid_uid');
            $table->decimal('amount', 10, 2);
            $table->enum('payment_method', ['cash', 'gcash']);
            $table->timestamp('payment_date')->useCurrent();
            $table->timestamps();

            // Foreign key constraint to users table
            $table->foreign('rfid_uid')->references('rfid_uid')->on('users')->onDelete('cascade');
        });
    }

    public function down(): void {
        Schema::dropIfExists('members_payment');
    }
};

