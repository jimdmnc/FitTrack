<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddStatusAndReferenceToMembersPaymentTable extends Migration
{
    public function up()
    {
        Schema::table('members_payment', function (Blueprint $table) {
            // Add status column after payment_method
            $table->enum('status', ['pending', 'completed', 'failed'])
                  ->default('pending')
                  ->after('payment_method');

            // Add payment_reference column after status
            $table->string('payment_reference', 255)
                  ->nullable()
                  ->after('status');

            // Add composite index for frequent queries
            $table->index(['rfid_uid', 'status'], 'idx_rfid_status');
            
            // Single column index for payment_reference
            $table->index('payment_reference', 'idx_payment_reference');
        });
    }

    public function down()
    {
        Schema::table('members_payment', function (Blueprint $table) {
            // Drop indexes first (important for MySQL)
            $table->dropIndex('idx_rfid_status');
            $table->dropIndex('idx_payment_reference');
            
            // Then drop columns
            $table->dropColumn('status');
            $table->dropColumn('payment_reference');
        });
    }
}