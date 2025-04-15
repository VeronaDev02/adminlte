<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Renomear a tabela 'unidade' para 'tipo_unidade'
        Schema::rename('unidade', 'tipo_unidade');

        // Renomear as colunas
        Schema::table('tipo_unidade', function (Blueprint $table) {
            $table->renameColumn('uni_id', 'tip_id');
            $table->renameColumn('uni_codigo', 'tip_codigo');
            $table->renameColumn('uni_nome', 'tip_nome');
            $table->renameColumn('uni_cor', 'tip_cor');
        });

        // Renomear as constraints únicas
        DB::statement('ALTER TABLE tipo_unidade RENAME CONSTRAINT unidade_uni_codigo_unique TO tipo_unidade_tip_codigo_unique');
        DB::statement('ALTER TABLE tipo_unidade RENAME CONSTRAINT unidade_uni_cor_unique TO tipo_unidade_tip_cor_unique');
    }

    public function down(): void
    {
        // Reverter nome das constraints
        DB::statement('ALTER TABLE tipo_unidade RENAME CONSTRAINT tipo_unidade_tip_codigo_unique TO unidade_uni_codigo_unique');
        DB::statement('ALTER TABLE tipo_unidade RENAME CONSTRAINT tipo_unidade_tip_cor_unique TO unidade_uni_cor_unique');

        // Reverter nome das colunas
        Schema::table('tipo_unidade', function (Blueprint $table) {
            $table->renameColumn('tip_id', 'uni_id');
            $table->renameColumn('tip_codigo', 'uni_codigo');
            $table->renameColumn('tip_nome', 'uni_nome');
            $table->renameColumn('tip_cor', 'uni_cor');
        });

        // Reverter nome da tabela
        Schema::rename('tipo_unidade', 'unidade');
    }
};