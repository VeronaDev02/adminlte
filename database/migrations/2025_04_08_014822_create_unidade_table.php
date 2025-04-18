<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up(): void
    {
        Schema::create('unidade', function (Blueprint $table) {
            $table->id('uni_id');
            $table->string('uni_codigo');
            $table->string('uni_descricao');
            $table->string('uni_cidade');
            $table->string('uni_uf', 2);
            $table->timestamps();
        });
    }


    public function down(): void
    {
        Schema::dropIfExists('unidade');
    }
};