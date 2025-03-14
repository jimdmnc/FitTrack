<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('attendances', function (Blueprint $table) {
            $table->string('rfid_uid', 50)->change(); // Modify rfid_uid column
            $table->foreign('rfid_uid')->references('rfid_uid')->on('users')->onDelete('cascade'); // Ensure foreign key
        });
    }

    public function down()
    {
        Schema::table('attendances', function (Blueprint $table) {
            $table->dropForeign(['rfid_uid']); // Drop foreign key
        });
    }
};