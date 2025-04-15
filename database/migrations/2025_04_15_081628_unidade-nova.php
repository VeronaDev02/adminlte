<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('unidade', function (Blueprint $table) {
            $table->id('uni_id');
            $table->bigInteger('uni_codigo')->unique();
            $table->unsignedBigInteger('uni_tip_id');
            $table->timestamps();

            // Chave estrangeira referenciando a tabela tipo_unidade
            $table->foreign('uni_tip_id')
                  ->references('tip_id')
                  ->on('tipo_unidade')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('unidade');
    }
};