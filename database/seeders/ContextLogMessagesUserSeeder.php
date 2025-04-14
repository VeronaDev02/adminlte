<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ContextLogMessagesUser;

class ContextLogMessagesUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $contexts = [
            // User
            ['id' => 1, 'context' => 'Login de usuário'],
            ['id' => 2, 'context' => 'Logout de usuário'],
            ['id' => 3, 'context' => 'Criação de usuário'],
            ['id' => 4, 'context' => 'Edição de usuário'],
            ['id' => 5, 'context' => 'Exclusão de usuário'],
            ['id' => 6, 'context' => 'Alteração de senha de usuário'],
            ['id' => 7, 'context' => 'Edição de perfil de usuário'],
            
            // Role
            ['id' => 8, 'context' => 'Criação de função/cargo'],
            ['id' => 9, 'context' => 'Edição de função/cargo'],
            ['id' => 10, 'context' => 'Exclusão de função/cargo'],
            
            // Selfs
            ['id' => 11, 'context' => 'Criação de self'],
            ['id' => 12, 'context' => 'Edição de self'],
            ['id' => 13, 'context' => 'Exclusão de self'],
            
            // Unidade
            ['id' => 14, 'context' => 'Criação de unidade'],
            ['id' => 15, 'context' => 'Edição de unidade'],
            ['id' => 16, 'context' => 'Exclusão de unidade'],
            
            // Unit
            ['id' => 17, 'context' => 'Criação de unit'],
            ['id' => 18, 'context' => 'Edição de unit'],
            ['id' => 19, 'context' => 'Exclusão de unit'],
        ];

        foreach ($contexts as $context) {
            ContextLogMessagesUser::updateOrCreate(
                ['id' => $context['id']],
                ['context' => $context['context']]
            );
        }
    }
}