<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class TipoUnidadeTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('tipo_unidade')->delete();
        
        \DB::table('tipo_unidade')->insert(array (
            0 => 
            array (
                'tip_id' => 1,
                'created_at' => NULL,
                'updated_at' => NULL,
                'tip_codigo' => 1,
                'tip_nome' => 'Verona',
                'tip_cor' => 'orange',
            ),
            1 => 
            array (
                'tip_id' => 2,
                'created_at' => NULL,
                'updated_at' => NULL,
                'tip_codigo' => 2,
                'tip_nome' => 'Box Atacadista',
                'tip_cor' => 'blue',
            ),
            2 => 
            array (
                'tip_id' => 3,
                'created_at' => NULL,
                'updated_at' => NULL,
                'tip_codigo' => 3,
                'tip_nome' => 'Bosto - Route 366',
                'tip_cor' => 'green',
            ),
            3 => 
            array (
                'tip_id' => 4,
                'created_at' => NULL,
                'updated_at' => NULL,
                'tip_codigo' => 4,
                'tip_nome' => 'Restaurante',
                'tip_cor' => 'red',
            ),
            4 => 
            array (
                'tip_id' => 5,
                'created_at' => NULL,
                'updated_at' => NULL,
                'tip_codigo' => 5,
                'tip_nome' => 'Adega',
                'tip_cor' => 'gray',
            ),
            5 => 
            array (
                'tip_id' => 6,
                'created_at' => NULL,
                'updated_at' => NULL,
                'tip_codigo' => 6,
                'tip_nome' => 'Farmacia',
                'tip_cor' => 'info',
            ),
            6 => 
            array (
                'tip_id' => 7,
                'created_at' => NULL,
                'updated_at' => NULL,
                'tip_codigo' => 7,
                'tip_nome' => 'Central ADM',
                'tip_cor' => 'white',
            ),
            7 => 
            array (
                'tip_id' => 8,
                'created_at' => NULL,
                'updated_at' => NULL,
                'tip_codigo' => 8,
                'tip_nome' => 'Industria de Pães',
                'tip_cor' => 'yellow',
            ),
        ));
        
        
    }
}