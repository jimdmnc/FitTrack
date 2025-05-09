<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;

class ChangePaymentScreenshotTypeInMembersPaymentTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        // Change the column to LONGTEXT
        DB::statement('ALTER TABLE members_payment MODIFY payment_screenshot LONGTEXT');
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        // Revert the column back to VARCHAR(255)
        DB::statement('ALTER TABLE members_payment MODIFY payment_screenshot VARCHAR(255)');
    }
}
