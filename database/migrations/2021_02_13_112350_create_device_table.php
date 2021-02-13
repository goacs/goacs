<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDeviceTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('device', function (Blueprint $table) {
            $table->id();
            $table->string('serial_number', 40);
            $table->string('oui', 10)->default('');
            $table->string('software_version', 35)->nullable();
            $table->string('hardware_version', 25)->nullable();
            $table->string('connection_request_url', 100);
            $table->string('connection_request_user', 100)->nullable();
            $table->string('connection_request_password', 100)->nullable();
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
        Schema::dropIfExists('device');
    }
}
