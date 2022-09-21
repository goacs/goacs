<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSessionIdColumnToLogs extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('logs', function (Blueprint $table) {
            $table->string('session_id', 64);
        });

        Schema::table('logs', function (Blueprint $table) {
            $table->index(['device_id', 'session_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('logs', function (Blueprint $table) {
            $table->dropIndex(['device_id', 'session_id']);
        });

        Schema::table('logs', function (Blueprint $table) {
            $table->dropColumn('session_id');
        });
    }
}
