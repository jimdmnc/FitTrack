<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('foods', function (Blueprint $table) {
            $table->id();
            $table->string('foodName');
            $table->double('calories');
            $table->double('protein');
            $table->double('fats');
            $table->double('carbs');
            $table->integer('grams');
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('foods');
    }
};
