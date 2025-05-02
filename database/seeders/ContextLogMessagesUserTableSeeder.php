<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class ContextLogMessagesUserTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('context_log_messages_user')->delete();
        
        \DB::table('context_log_messages_user')->insert(array (
            0 => 
            array (
                'id' => 1,
                'context' => 'Login de usuário',
                'created_at' => '2025-04-09 17:22:10',
                'updated_at' => '2025-04-09 17:22:10',
            ),
            1 => 
            array (
                'id' => 2,
                'context' => 'Logout de usuário',
                'created_at' => '2025-04-09 17:22:10',
                'updated_at' => '2025-04-09 17:22:10',
            ),
            2 => 
            array (
                'id' => 3,
                'context' => 'Criação de usuário',
                'created_at' => '2025-04-09 17:22:10',
                'updated_at' => '2025-04-09 17:22:10',
            ),
            3 => 
            array (
                'id' => 4,
                'context' => 'Edição de usuário',
                'created_at' => '2025-04-09 17:22:10',
                'updated_at' => '2025-04-09 17:22:10',
            ),
            4 => 
            array (
                'id' => 5,
                'context' => 'Exclusão de usuário',
                'created_at' => '2025-04-09 17:22:10',
                'updated_at' => '2025-04-09 17:22:10',
            ),
            5 => 
            array (
                'id' => 6,
                'context' => 'Alteração de senha de usuário',
                'created_at' => '2025-04-09 17:22:10',
                'updated_at' => '2025-04-09 17:22:10',
            ),
            6 => 
            array (
                'id' => 7,
                'context' => 'Edição de perfil de usuário',
                'created_at' => '2025-04-09 17:22:10',
                'updated_at' => '2025-04-09 17:22:10',
            ),
            7 => 
            array (
                'id' => 8,
                'context' => 'Criação de função/cargo',
                'created_at' => '2025-04-09 17:22:10',
                'updated_at' => '2025-04-09 17:22:10',
            ),
            8 => 
            array (
                'id' => 9,
                'context' => 'Edição de função/cargo',
                'created_at' => '2025-04-09 17:22:10',
                'updated_at' => '2025-04-09 17:22:10',
            ),
            9 => 
            array (
                'id' => 10,
                'context' => 'Exclusão de função/cargo',
                'created_at' => '2025-04-09 17:22:10',
                'updated_at' => '2025-04-09 17:22:10',
            ),
            10 => 
            array (
                'id' => 11,
                'context' => 'Criação de self',
                'created_at' => '2025-04-09 17:22:10',
                'updated_at' => '2025-04-09 17:22:10',
            ),
            11 => 
            array (
                'id' => 12,
                'context' => 'Edição de self',
                'created_at' => '2025-04-09 17:22:10',
                'updated_at' => '2025-04-09 17:22:10',
            ),
            12 => 
            array (
                'id' => 13,
                'context' => 'Exclusão de self',
                'created_at' => '2025-04-09 17:22:10',
                'updated_at' => '2025-04-09 17:22:10',
            ),
            13 => 
            array (
                'id' => 14,
                'context' => 'Criação de unidade',
                'created_at' => '2025-04-09 17:22:10',
                'updated_at' => '2025-04-09 17:22:10',
            ),
            14 => 
            array (
                'id' => 15,
                'context' => 'Edição de unidade',
                'created_at' => '2025-04-09 17:22:10',
                'updated_at' => '2025-04-09 17:22:10',
            ),
            15 => 
            array (
                'id' => 16,
                'context' => 'Exclusão de unidade',
                'created_at' => '2025-04-09 17:22:10',
                'updated_at' => '2025-04-09 17:22:10',
            ),
            16 => 
            array (
                'id' => 20,
                'context' => 'Tentativa de login',
                'created_at' => '2025-04-10 15:26:07',
                'updated_at' => '2025-04-10 15:26:07',
            ),
            17 => 
            array (
                'id' => 17,
            'context' => 'Criação de unit (user/unidade)',
                'created_at' => '2025-04-09 17:22:10',
                'updated_at' => '2025-04-09 17:22:10',
            ),
            18 => 
            array (
                'id' => 18,
            'context' => 'Edição de unit (user/unidade)',
                'created_at' => '2025-04-09 17:22:10',
                'updated_at' => '2025-04-09 17:22:10',
            ),
            19 => 
            array (
                'id' => 19,
            'context' => 'Exclusão de unit (user/unidade)',
                'created_at' => '2025-04-09 17:22:10',
                'updated_at' => '2025-04-09 17:22:10',
            ),
        ));
        
        
    }
}