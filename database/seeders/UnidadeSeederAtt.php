<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UnidadeSeederAtt extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Limpa a tabela antes de inserir novos dados
        DB::table('unidade')->truncate();

        // Dados para inserir
        $dados = [
            [
                'uni_codigo' => 1,
                'uni_nome' => 'Verona',
                'uni_cor' => 'orange'
            ],
            [
                'uni_codigo' => 2,
                'uni_nome' => 'Box Atacadista',
                'uni_cor' => 'blue'
            ],
            [
                'uni_codigo' => 3,
                'uni_nome' => 'Bosto - Route 366',
                'uni_cor' => 'green'
            ],
            [
                'uni_codigo' => 4,
                'uni_nome' => 'Restaurante',
                'uni_cor' => 'red'
            ],
            [
                'uni_codigo' => 5,
                'uni_nome' => 'Adega',
                'uni_cor' => 'gray'
            ],
            [
                'uni_codigo' => 6,
                'uni_nome' => 'Farmacia',
                'uni_cor' => 'info'
            ],
            [
                'uni_codigo' => 7,
                'uni_nome' => 'Central ADM',
                'uni_cor' => 'white'
            ],
            [
                'uni_codigo' => 8,
                'uni_nome' => 'Industria de Pães',
                'uni_cor' => 'yellow'
            ]
        ];

        // Insere os dados
        DB::table('unidade')->insert($dados);
    }
}