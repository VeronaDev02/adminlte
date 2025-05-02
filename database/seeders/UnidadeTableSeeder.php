<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class UnidadeTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('unidade')->delete();
        
        \DB::table('unidade')->insert(array (
            0 => 
            array (
                'uni_id' => 31,
                'uni_codigo' => '004',
                'uni_tip_id' => 2,
                'created_at' => '2025-04-25 10:17:44',
                'updated_at' => '2025-04-25 10:17:44',
                'uni_nome' => 'Cornélio Procópio',
            ),
        ));
        
        
    }
}