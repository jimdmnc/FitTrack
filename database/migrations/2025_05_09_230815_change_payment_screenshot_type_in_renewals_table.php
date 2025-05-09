<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;

class ChangePaymentScreenshotTypeInRenewalsTable extends Migration
{
    public function up()
    {
        DB::statement('ALTER TABLE renewals MODIFY payment_screenshot LONGTEXT');
    }

    public function down()
    {
        // Change back to original type (e.g., VARCHAR(255) if originally a string)
        DB::statement('ALTER TABLE renewals MODIFY payment_screenshot VARCHAR(255)');
    }
}
