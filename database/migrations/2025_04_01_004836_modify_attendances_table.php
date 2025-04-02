<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */

public function down()
{
    Schema::table('attendances', function (Blueprint $table) {
        // Drop the foreign key constraint
        $table->dropForeign(['session_id']);
        // Drop the session_id column
        $table->dropColumn('session_id');
        // Drop the check_in_method column
        $table->dropColumn('check_in_method');
    });
}


};
