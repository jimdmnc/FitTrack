<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
// Create a migration file
// php artisan make:migration create_wifi_credentials_table

// In the migration file:
public function up()
{
    Schema::create('wifi_credentials', function (Blueprint $table) {
        $table->id();
        $table->string('ssid');
        $table->text('password');
        $table->boolean('is_active')->default(true);
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wifi_credentials');
    }
};
