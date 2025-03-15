<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            // Add new columns or modify existing ones here
            $table->string('status')->nullable()->after('time_out'); // Example new column
        });
    }

    public function down(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            // Reverse changes if needed
            $table->dropColumn('status');
        });
    }
};
