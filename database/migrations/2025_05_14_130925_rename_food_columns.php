// database/migrations/2025_05_14_130100_rename_food_columns.php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RenameFoodColumns extends Migration
{
    public function up()
    {
        Schema::table('foods', function (Blueprint $table) {
            $table->renameColumn('calories', 'consumed_calories');
            $table->renameColumn('protein', 'consumed_protein');
            $table->renameColumn('fats', 'consumed_fats');
            $table->renameColumn('carbs', 'consumed_carbs');
        });
    }

    public function down()
    {
        Schema::table('foods', function (Blueprint $table) {
            $table->renameColumn('consumed_calories', 'calories');
            $table->renameColumn('consumed_protein', 'protein');
            $table->renameColumn('consumed_fats', 'fats');
            $table->renameColumn('consumed_carbs', 'carbs');
        });
    }
}