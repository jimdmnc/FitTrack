<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::dropIfExists('payments');
        Schema::dropIfExists('members');
    }

    public function down(): void
    {
        // No need to recreate the tables since we only want to delete them
    }
};

