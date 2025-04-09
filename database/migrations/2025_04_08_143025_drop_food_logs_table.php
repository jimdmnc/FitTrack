<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DropFoodLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('food_logs');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Optionally, you can recreate the table in the down method if needed
        Schema::create('food_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('food_id')->constrained('foods')->onDelete('cascade');
            $table->foreignId('rfid_uid')->constrained('users')->onDelete('cascade');
            $table->decimal('quantity', 8, 2);
            $table->date('date');
            $table->decimal('total_calories', 8, 2);
            $table->decimal('total_protein', 8, 2);
            $table->decimal('total_fats', 8, 2);
            $table->decimal('total_carbs', 8, 2);
            $table->timestamps();
        });
    }
}
