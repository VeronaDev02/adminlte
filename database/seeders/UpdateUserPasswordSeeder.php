<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UpdateUserPasswordSeeder extends Seeder
{
    public function run()
    {
        $senha = 'admin123';
        $hashSenha = Hash::make($senha);
        
        $this->command->info('Hash gerado para a senha admin123: ' . $hashSenha);
        
        $updated = DB::table('users')
            ->where('use_username', 'lucas_costa')
            ->update([
                'use_password' => $hashSenha,
                'updated_at' => now()
            ]);
        
        if ($updated) {
            $this->command->info('Senha atualizada com sucesso!');
        } else {
            $this->command->error('Usuário não encontrado!');
        }
    }
}