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
        Schema::table('guides', function (Blueprint $table) {
            $table->text('profile')->nullable();
            $table->text('car_photo')->nullable();
            $table->string('car_type')->nullable();
            $table->string('plat_number')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('guides', function (Blueprint $table) {
            $table->dropColumn('profile');
            $table->dropColumn('car_photo');
            $table->dropColumn('car_type');
            $table->dropColumn('plat_number');
        });
    }
};
