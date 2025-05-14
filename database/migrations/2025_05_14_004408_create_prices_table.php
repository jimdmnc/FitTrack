<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreatePricesTable extends Migration
{
    /**
     * Run the migration.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('prices', function (Blueprint $table) {
            $table->id();
            $table->string('type');
            $table->decimal('amount', 10, 2);
            $table->timestamps();
        });

        // Insert default price types
        $this->seedDefaultPrices();
    }

    /**
     * Reverse the migration.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('prices');
    }

    /**
     * Seed default price types.
     *
     * @return void
     */
    private function seedDefaultPrices()
    {
        $defaultPrices = [
            ['type' => 'session', 'amount' => 60.00],
            ['type' => 'weekly', 'amount' => 300.00],
            ['type' => 'monthly', 'amount' => 850.00],
            ['type' => 'annual', 'amount' => 10000.00],
        ];

        foreach ($defaultPrices as $price) {
            DB::table('prices')->insert([
                'type' => $price['type'],
                'amount' => $price['amount'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}