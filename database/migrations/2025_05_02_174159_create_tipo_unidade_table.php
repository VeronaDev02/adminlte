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
        Schema::create('tipo_unidade', function (Blueprint $table) {
            $table->bigIncrements('tip_id');
            $table->timestamps();
            $table->bigInteger('tip_codigo')->nullable()->unique();
            $table->string('tip_nome')->nullable();
            $table->string('tip_cor')->nullable()->unique();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tipo_unidade');
    }
};
