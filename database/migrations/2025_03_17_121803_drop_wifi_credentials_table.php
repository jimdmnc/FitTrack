<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::dropIfExists('wifi_credentials');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::create('wifi_credentials', function (Blueprint $table) {
            $table->id();
            $table->string('ssid');
            $table->string('password');
            $table->timestamps();
        });
    }
};
