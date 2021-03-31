<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class MicrotimeOnTimestampsInLogs extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        \Illuminate\Support\Facades\DB::raw('alter table logs modify created_at timestamp(6) null;');
        \Illuminate\Support\Facades\DB::raw('alter table logs modify updated_at timestamp(6) null;');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        \Illuminate\Support\Facades\DB::raw('alter table logs modify created_at timestamp null;');
        \Illuminate\Support\Facades\DB::raw('alter table logs modify updated_at timestamp null;');
    }
}
