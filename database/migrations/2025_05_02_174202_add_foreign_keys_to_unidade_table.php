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
        Schema::table('unidade', function (Blueprint $table) {
            $table->foreign(['uni_tip_id'])->references(['tip_id'])->on('tipo_unidade')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('unidade', function (Blueprint $table) {
            $table->dropForeign('unidade_uni_tip_id_foreign');
        });
    }
};
