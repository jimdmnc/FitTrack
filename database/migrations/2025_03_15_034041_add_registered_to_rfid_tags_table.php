<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('rfid_tags', function (Blueprint $table) {
            $table->boolean('registered')->default(false)->after('uid');
        });
    }
    
    public function down()
    {
        Schema::table('rfid_tags', function (Blueprint $table) {
            $table->dropColumn('registered');
        });
    }
};
