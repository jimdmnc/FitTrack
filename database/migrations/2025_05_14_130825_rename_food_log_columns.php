// database/migrations/2025_05_14_130000_rename_food_log_columns.php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RenameFoodLogColumns extends Migration
{
    public function up()
    {
        Schema::table('food_logs', function (Blueprint $table) {
            $table->renameColumn('total_calories', 'consumed_calories');
            $table->renameColumn('total_protein', 'consumed_protein');
            $table->renameColumn('total_fats', 'consumed_fats');
            $table->renameColumn('total_carbs', 'consumed_carbs');
        });
    }

    public function down()
    {
        Schema::table('food_logs', function (Blueprint $table) {
            $table->renameColumn('consumed_calories', 'total_calories');
            $table->renameColumn('consumed_protein', 'total_protein');
            $table->renameColumn('consumed_fats', 'total_fats');
            $table->renameColumn('consumed_carbs', 'total_carbs');
        });
    }
}