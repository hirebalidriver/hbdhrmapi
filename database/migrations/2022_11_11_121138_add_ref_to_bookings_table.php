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
            $table->string('ref_id')->nullable();
            $table->string('name')->nullable();
            $table->string('phone')->nullable();
            $table->text('hotel')->nullable();
            $table->string('status_payment')->nullable();
            $table->integer('collect')->nullable();
            $table->integer('option_id')->nullable();
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
            $table->dropColumn('ref_id');
            $table->dropColumn('name');
            $table->dropColumn('phone');
            $table->dropColumn('hotel');
            $table->dropColumn('status_payment');
            $table->dropColumn('collect');
            $table->dropColumn('option_id');
        });
    }
};
