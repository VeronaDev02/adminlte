<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Renomear a coluna de 'nome' para 'uni_nome'
        DB::statement('ALTER TABLE unidade RENAME COLUMN nome TO uni_nome');
        
        // Garantir que a restrição única seja aplicada corretamente
        Schema::table('unidade', function (Blueprint $table) {
            // Verificar e remover a restrição antiga se existir
            $sm = Schema::getConnection()->getDoctrineSchemaManager();
            $indexes = $sm->listTableIndexes('unidade');
            
            if (array_key_exists('unidade_nome_unique', $indexes)) {
                $table->dropUnique('unidade_nome_unique');
            }
            
            // Adicionar a nova restrição
            $table->unique('uni_nome', 'unidade_uni_nome_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remover a restrição de unicidade
        Schema::table('unidade', function (Blueprint $table) {
            $table->dropUnique('unidade_uni_nome_unique');
        });
        
        // Renomear de volta de 'uni_nome' para 'nome'
        DB::statement('ALTER TABLE unidade RENAME COLUMN uni_nome TO nome');
        
        // Recriar a restrição original
        Schema::table('unidade', function (Blueprint $table) {
            $table->unique('nome', 'unidade_nome_unique');
        });
    }
};