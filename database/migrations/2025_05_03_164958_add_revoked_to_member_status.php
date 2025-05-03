<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRevokedToMemberStatus extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->enum('member_status', ['active', 'expired', 'revoked'])
                  ->default('active')
                  ->change();
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->enum('member_status', ['active', 'expired'])
                  ->default('active')
                  ->change();
        });
    }
}