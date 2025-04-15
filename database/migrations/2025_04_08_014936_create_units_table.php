<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up(): void
    {
        Schema::create('units', function (Blueprint $table) {
            $table->id('unit_id');
            $table->unsignedBigInteger('unit_uni_id');
            $table->unsignedBigInteger('unit_use_id');
            $table->timestamps();

            $table->foreign('unit_uni_id')
                ->references('uni_id')
                ->on('unidade')
                ->onDelete('cascade');
                
            $table->foreign('unit_use_id')
                ->references('use_id')
                ->on('users')
                ->onDelete('cascade');
                
            // Garantir que não haja duplicatas na relação
            $table->unique(['unit_uni_id', 'unit_use_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('units');
    }
};