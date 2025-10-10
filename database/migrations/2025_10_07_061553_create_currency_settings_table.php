<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('currency_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->decimal('value', 15, 2);
            $table->string('description')->nullable();
            $table->timestamps();
        });
        
        // Insert default conversion rate
        DB::table('currency_settings')->insert([
            'key' => 'usd_to_idr_rate',
            'value' => 16500.00,
            'description' => 'USD to IDR conversion rate',
            'created_at' => now(),
            'updated_at' => now()
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('currency_settings');
    }
};
