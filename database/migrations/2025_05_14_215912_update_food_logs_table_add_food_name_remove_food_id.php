<?php

   use Illuminate\Database\Migrations\Migration;
   use Illuminate\Database\Schema\Blueprint;
   use Illuminate\Support\Facades\Schema;

   class UpdateFoodLogsTableAddFoodNameRemoveFoodId extends Migration
   {
       public function up()
       {
           Schema::table('food_logs', function (Blueprint $table) {
               // Drop foreign key constraint
               $table->dropForeign(['food_id']);
               // Drop food_id column
               $table->dropColumn('food_id');
               // Add food_name column
               $table->string('food_name', 255)->after('id');
           });
       }

       public function down()
       {
           Schema::table('food_logs', function (Blueprint $table) {
               // Reverse: drop food_name
               $table->dropColumn('food_name');
               // Reverse: add food_id
               $table->unsignedBigInteger('food_id')->after('id');
               // Reverse: add foreign key
               $table->foreign('food_id')->references('id')->on('foods')->onDelete('cascade');
           });
       }
   }