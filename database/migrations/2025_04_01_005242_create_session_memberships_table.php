<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('session_memberships', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');  // Linking to the user table
            $table->enum('status', ['active', 'expired']);
            $table->date('session_start');
            $table->date('session_end');
            $table->timestamps();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');  // If user is deleted, remove related sessions

        });
    }
    
    public function down()
    {
        Schema::dropIfExists('session_memberships');
    }
    
};
