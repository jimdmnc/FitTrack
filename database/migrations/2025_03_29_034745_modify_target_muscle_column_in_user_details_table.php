<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ModifyTargetMuscleColumnInUserDetailsTable extends Migration
{
    public function up()
    {
        Schema::table('user_details', function (Blueprint $table) {
            $table->text('target_muscle')->change();  // Or $table->json('target_muscle')->change();
        });
    }

    public function down()
    {
            $table->enum('target_muscle', ['Back', 'Chest', 'Arms', 'Core', 'Legs', 'Full Body'])->nullable()->change();
        });
    }
}
