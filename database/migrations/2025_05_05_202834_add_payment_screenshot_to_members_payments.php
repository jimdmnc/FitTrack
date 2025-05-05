<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('members_payment', function (Blueprint $table) {
            // Drop the 'payment_reference' column
            $table->dropColumn('payment_reference');

            // Add new columns
            $table->string('payment_screenshot')->nullable()->after('id'); // adjust the 'after' column as needed
            $table->string('verified_by')->nullable()->after('payment_screenshot');
            $table->timestamp('verified_at')->nullable()->after('verified_by');
        });
    }

    public function down()
    {
        Schema::table('members_payments', function (Blueprint $table) {
            // Restore dropped column
            $table->string('payment_reference')->nullable(); // adjust data type if it was different

            // Drop the added columns
            $table->dropColumn(['payment_screenshot', 'verified_by', 'verified_at']);
        });
    }
};
