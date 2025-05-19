<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSelOriginPortToSelfsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('selfs', function (Blueprint $table) {
            $table->text('sel_origin_port')->nullable()->after('sel_pdv_listen_port');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('selfs', function (Blueprint $table) {
            $table->dropColumn('sel_origin_port');
        });
    }
}
