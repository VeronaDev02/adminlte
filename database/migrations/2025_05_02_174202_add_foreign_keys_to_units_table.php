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
        Schema::table('units', function (Blueprint $table) {
            $table->foreign(['unit_use_id'])->references(['use_id'])->on('users')->onDelete('CASCADE');
            $table->foreign(['unit_uni_id'], 'fk_units_unidade_new')->references(['uni_id'])->on('unidade')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('units', function (Blueprint $table) {
            $table->dropForeign('units_unit_use_id_foreign');
            $table->dropForeign('fk_units_unidade_new');
        });
    }
};
