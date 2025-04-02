<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('session_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('rfid_uid');
            $table->timestamp('checked_in_at');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('session_logs');
    }
};
