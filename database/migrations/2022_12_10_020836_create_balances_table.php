<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
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
        Schema::create('balances', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('guide_id');
            $table->bigInteger('trx_id')->nullable();
            $table->bigInteger('balance')->default(0);
            $table->bigInteger('in')->default(0);
            $table->bigInteger('out')->default(0);
            $table->bigInteger('fee')->default(0);
            $table->boolean('lock')->default(false);
            $table->string('type')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('balances');
    }
};
