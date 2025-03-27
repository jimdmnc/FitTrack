<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('user_details', function (Blueprint $table) {
            $table->enum('goal', ['Gain Muscle', 'Lose Weight', 'Maintain'])->after('activity_level');
            $table->json('target_muscle')->nullable()->after('goal');
        });
    }

    public function down(): void
    {
        Schema::table('user_details', function (Blueprint $table) {
            $table->dropColumn(['goal', 'target_muscle']);
        });
    }
};
