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
            $table->bigInteger('bill_total')->default(0);
            $table->bigInteger('susuk_guide')->default(0);
            $table->bigInteger('susuk_hbd')->default(0);
            $table->bigInteger('tiket_total')->default(0);
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
            $table->dropColumn('bill_total');
            $table->dropColumn('susuk_guide');
            $table->dropColumn('susuk_hbd');
            $table->dropColumn('tiket_total');
        });
    }
};
