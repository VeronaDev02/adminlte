<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class RoleTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('role')->delete();
        
        \DB::table('role')->insert(array (
            0 => 
            array (
                'rol_id' => 9,
                'rol_name' => 'Shinigami',
                'created_at' => '2025-04-09 17:17:33',
                'updated_at' => '2025-04-17 09:01:17',
            ),
            1 => 
            array (
                'rol_id' => 43,
                'rol_name' => 'Operador de Caixa Self Checkout',
                'created_at' => '2025-04-25 09:07:28',
                'updated_at' => '2025-04-25 09:07:28',
            ),
            2 => 
            array (
                'rol_id' => 1,
                'rol_name' => 'Administrador',
                'created_at' => '2025-04-08 11:02:23',
                'updated_at' => '2025-04-09 16:00:46',
            ),
        ));
        
        
    }
}