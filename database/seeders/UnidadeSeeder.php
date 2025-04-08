<?php

namespace Database\Seeders;

use App\Models\Unidade;
use Illuminate\Database\Seeder;

class UnidadeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $unidades = [
            [
                'uni_codigo' => '001',
                'uni_descricao' => 'Matriz',
                'uni_cidade' => 'São Paulo',
                'uni_uf' => 'SP',
            ],
            [
                'uni_codigo' => '002',
                'uni_descricao' => 'Filial Rio de Janeiro',
                'uni_cidade' => 'Rio de Janeiro',
                'uni_uf' => 'RJ',
            ],
            [
                'uni_codigo' => '003',
                'uni_descricao' => 'Filial Belo Horizonte',
                'uni_cidade' => 'Belo Horizonte',
                'uni_uf' => 'MG',
            ],
            [
                'uni_codigo' => '004',
                'uni_descricao' => 'Filial Porto Alegre',
                'uni_cidade' => 'Porto Alegre',
                'uni_uf' => 'RS',
            ],
            [
                'uni_codigo' => '005',
                'uni_descricao' => 'Filial Recife',
                'uni_cidade' => 'Recife',
                'uni_uf' => 'PE',
            ],
        ];

        foreach ($unidades as $unidade) {
            Unidade::create($unidade);
        }
    }
}