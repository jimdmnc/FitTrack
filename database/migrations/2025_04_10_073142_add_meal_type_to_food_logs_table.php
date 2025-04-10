<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMealTypeToFoodLogsTable extends Migration
{
    public function up()
    {
        Schema::table('food_logs', function (Blueprint $table) {
            $table->string('meal_type', 50)->after('rfid_uid');
        });
    }

    public function down()
    {
        Schema::table('food_logs', function (Blueprint $table) {
            $table->dropColumn('meal_type');
        });
    }
}
