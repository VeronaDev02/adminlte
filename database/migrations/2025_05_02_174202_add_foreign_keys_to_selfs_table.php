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
        Schema::table('selfs', function (Blueprint $table) {
            $table->foreign(['sel_uni_id'])->references(['uni_id'])->on('unidade')->onUpdate('CASCADE')->onDelete('RESTRICT');
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
            $table->dropForeign('selfs_sel_uni_id_foreign');
        });
    }
};
