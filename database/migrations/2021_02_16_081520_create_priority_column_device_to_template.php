<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePriorityColumnDeviceToTemplate extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('device_to_template', function (Blueprint $table) {
            $table->integer('priority')->default(100);
            $table->unique(['device_id', 'priority']);
            $table->unique(['device_id', 'template_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('device_to_template', function (Blueprint $table) {
            $table->dropUnique(['device_id', 'priority']);
            $table->dropUnique(['device_id', 'template_id']);
            $table->dropColumn('priority');
        });
    }
}
