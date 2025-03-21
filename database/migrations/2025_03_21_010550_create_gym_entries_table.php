<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGymEntriesTable extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('gym_entries')) {
            Schema::create('gym_entries', function (Blueprint $table) {
                $table->id(); // Primary key
                $table->foreignId('rfid_uid')->constrained()->onDelete('cascade'); // Foreign key to users table
                $table->timestamp('entry_time')->useCurrent(); // Timestamp of the gym entry
                $table->timestamps(); // created_at and updated_at
            });
        }
    }
    

    public function down()
    {
        Schema::dropIfExists('gym_entries');
    }
}
