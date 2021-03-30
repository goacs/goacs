<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RenameFaultsTableToLogs extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::rename('faults', 'logs');
        Schema::table('logs', function (Blueprint $table) {
            $table->string('type',32)->index()->default('fault');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::rename('logs', 'faults');
        Schema::table('faults', function (Blueprint $table) {
            $table->dropColumn('type');
        });
    }
}
