<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('revoke_reason')->nullable()->after('member_status');
            $table->timestamp('revoked_at')->nullable()->after('revoke_reason');
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['revoke_reason', 'revoked_at']);
        });
    }
};
