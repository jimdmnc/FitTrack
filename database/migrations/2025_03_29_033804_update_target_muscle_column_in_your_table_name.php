<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateTargetMuscleColumnInYourTableName extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user_details', function (Blueprint $table) {
            // Change target_muscle column to use enum type
            // If your DB doesn't support ENUM type, use a string with a length limitation
            $table->enum('target_muscle', ['Back', 'Chest', 'Arms', 'Core', 'Legs', 'Full Body'])->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('user_details', function (Blueprint $table) {
            // Reverse the enum change if rolling back the migration (e.g., revert to string or previous type)
            $table->string('target_muscle')->nullable()->change();
        });
    }
}
