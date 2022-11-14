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
        Schema::table('bookings', function (Blueprint $table) {
            $table->string('country')->nullable();
            $table->integer('adult')->default(0)->nullable();
            $table->integer('child')->default(0)->nullable();
            $table->bigInteger('price')->default(0)->nullable();
            $table->bigInteger('down_payment')->default(0)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn('country');
            $table->dropColumn('adult');
            $table->dropColumn('child');
            $table->dropColumn('price');
            $table->dropColumn('down_payment');
        });
    }
};
