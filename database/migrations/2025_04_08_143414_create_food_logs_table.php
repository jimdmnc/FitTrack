<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFoodLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('food_logs', function (Blueprint $table) {
            $table->id(); // Auto-increment primary key
            $table->unsignedBigInteger('food_id');
            $table->string('rfid_uid'); // Match the type of rfid_uid in users table
            $table->decimal('quantity', 8, 2); // Quantity of food consumed (e.g., in kg)
            $table->date('date'); // Date of the food log entry
            $table->decimal('total_calories', 8, 2); // Total calories for the food log entry
            $table->decimal('total_protein', 8, 2); // Total protein for the food log entry
            $table->decimal('total_fats', 8, 2); // Total fats for the food log entry
            $table->decimal('total_carbs', 8, 2); // Total carbs for the food log entry
            $table->timestamps(); // Created at and updated at timestamps

            // Foreign key constraints with cascade on delete
            $table->foreign('food_id')->references('id')->on('foods')->onDelete('cascade');
            $table->foreign('rfid_uid')->references('rfid_uid')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('food_logs');
    }
}
