<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('user_details', function (Blueprint $table) {
            // Drop the foreign key constraint first (if it exists)
            $table->dropForeign(['rfid_uid']);

            // Drop the unique index (if it exists)
            $table->dropUnique(['rfid_uid']);

            // Drop the column itself
            $table->dropColumn('rfid_uid');

            // Re-add the rfid_uid column as a foreign key
            $table->string('rfid_uid')->after('id'); // Adjust position if needed
            $table->foreign('rfid_uid')->references('rfid_uid')->on('users')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::table('user_details', function (Blueprint $table) {
            // Drop the foreign key constraint before reverting
            $table->dropForeign(['rfid_uid']);
            $table->dropColumn('rfid_uid');

            // Restore old column definition
            $table->string('rfid_uid')->unique();
        });
    }
};
