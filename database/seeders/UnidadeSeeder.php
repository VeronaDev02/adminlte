<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UnidadeSeeder extends Seeder
{
    public function run()
    {
        DB::table('unidade')->insert([
            'uni_id' => 1,
            'uni_codigo' => 004,
            'uni_descricao' => 'Box Atacadista',
            'uni_cidade' => 'Cornélio Procópio',
            'uni_uf' => 'PR',
            'created_at' => '2025-04-08 11:02:24',
            'updated_at' => '2025-04-08 18:50:09',
        ]);
        DB::table('unidade')->insert([
            'uni_id' => 13,
            'uni_codigo' => 2,
            'uni_descricao' => 'Verona',
            'uni_cidade' => 'Arapongas',
            'uni_uf' => 'PR',
            'created_at' => '2025-04-09 17:13:25',
            'updated_at' => '2025-04-09 15:47:40',
        ]);
    }
}
