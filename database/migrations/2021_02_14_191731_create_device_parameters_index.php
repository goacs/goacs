<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDeviceParametersIndex extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('device_parameters', function (Blueprint $table) {
            $table->unique(['device_id', 'name']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('device_parameters', function (Blueprint $table) {
            $table->dropUnique(['device_id', 'name']);
        });
    }
}
