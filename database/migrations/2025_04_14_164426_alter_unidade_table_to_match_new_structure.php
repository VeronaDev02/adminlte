<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{

    public function up(): void
    {
        Schema::table('unidade', function (Blueprint $table) {
            $table->bigInteger('uni_codigo_new')->nullable();
        });

        DB::statement('UPDATE unidade SET uni_codigo_new = CAST(uni_codigo AS BIGINT)');

        Schema::table('unidade', function (Blueprint $table) {
            $table->dropColumn('uni_codigo');
        });

        DB::statement('ALTER TABLE unidade RENAME COLUMN uni_codigo_new TO uni_codigo');

        Schema::table('unidade', function (Blueprint $table) {
            if (!Schema::hasColumn('unidade', 'uni_nome')) {
                $table->string('uni_nome', 255)->nullable()->after('uni_codigo');
            }
            
            $table->string('uni_cor', 255)->nullable()->unique()->after('uni_nome');
            
            $table->dropColumn(['uni_descricao', 'uni_cidade', 'uni_uf']);
        });

        DB::statement('ALTER TABLE unidade ADD CONSTRAINT unidade_uni_codigo_unique UNIQUE (uni_codigo)');
    }

    public function down(): void
    {
        Schema::table('unidade', function (Blueprint $table) {
            $table->dropUnique('unidade_uni_codigo_unique');
            $table->dropUnique('unidade_uni_cor_unique');
            
            $table->dropColumn('uni_cor');

            if (Schema::hasColumn('unidade', 'uni_nome')) {
                $table->dropColumn('uni_nome');
            }

            $table->string('uni_codigo')->nullable();

            $table->string('uni_descricao')->nullable();
            $table->string('uni_cidade')->nullable();
            $table->string('uni_uf', 2)->nullable();
        });
    }
};