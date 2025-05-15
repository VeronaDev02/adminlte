<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDvrFieldsToSelfsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('selfs', function (Blueprint $table) {
            $table->text('sel_dvr_port')->nullable()->after('sel_dvr_porta');
            $table->text('sel_pdv_listen_port')->nullable()->after('sel_dvr_port');
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
            $table->dropColumn(['sel_dvr_port', 'sel_pdv_listen_port']);
        });
    }
}