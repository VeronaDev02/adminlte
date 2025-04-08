<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Role;
use App\Models\Unidade;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Obter IDs de roles
        $adminRoleId = Role::where('rol_name', 'Admin')->first()->rol_id;
        $gerenteRoleId = Role::where('rol_name', 'Gerente')->first()->rol_id;
        $usuarioRoleId = Role::where('rol_name', 'Usuário')->first()->rol_id;
        
        // Obter IDs de unidades
        $matrizId = Unidade::where('uni_codigo', '001')->first()->uni_id;
        $filialRjId = Unidade::where('uni_codigo', '002')->first()->uni_id;
        $filialMgId = Unidade::where('uni_codigo', '003')->first()->uni_id;
        
        // Criar administrador
        User::create([
            'use_name' => 'Administrador',
            'use_email' => 'admin@exemplo.com',
            'use_cod_func' => 'A001',
            'use_username' => 'admin',
            'use_password' => 'senha123', // Será criptografado automaticamente pelo setter
            'use_cell' => '(11) 99999-9999',
            'use_active' => true,
            'use_login_ativo' => true,
            'use_allow_updates' => true,
            'use_uni_id' => $matrizId,
            'use_rol_id' => $adminRoleId,
        ]);
        
        // Criar gerentes para cada filial
        $gerentes = [
            [
                'use_name' => 'Gerente São Paulo',
                'use_email' => 'gerente.sp@exemplo.com',
                'use_cod_func' => 'G001',
                'use_username' => 'gerente.sp',
                'use_password' => 'senha123',
                'use_cell' => '(11) 98888-8888',
                'use_uni_id' => $matrizId,
                'use_rol_id' => $gerenteRoleId,
            ],
            [
                'use_name' => 'Gerente Rio de Janeiro',
                'use_email' => 'gerente.rj@exemplo.com',
                'use_cod_func' => 'G002',
                'use_username' => 'gerente.rj',
                'use_password' => 'senha123',
                'use_cell' => '(21) 98888-8888',
                'use_uni_id' => $filialRjId,
                'use_rol_id' => $gerenteRoleId,
            ],
            [
                'use_name' => 'Gerente Belo Horizonte',
                'use_email' => 'gerente.bh@exemplo.com',
                'use_cod_func' => 'G003',
                'use_username' => 'gerente.bh',
                'use_password' => 'senha123',
                'use_cell' => '(31) 98888-8888',
                'use_uni_id' => $filialMgId,
                'use_rol_id' => $gerenteRoleId,
            ],
        ];
        
        foreach ($gerentes as $gerente) {
            User::create($gerente);
        }
        
        // Criar alguns usuários comuns
        $usuarios = [
            [
                'use_name' => 'Usuário São Paulo',
                'use_email' => 'usuario.sp@exemplo.com',
                'use_cod_func' => 'U001',
                'use_username' => 'usuario.sp',
                'use_password' => 'senha123',
                'use_cell' => '(11) 97777-7777',
                'use_uni_id' => $matrizId,
                'use_rol_id' => $usuarioRoleId,
            ],
            [
                'use_name' => 'Usuário Rio de Janeiro',
                'use_email' => 'usuario.rj@exemplo.com',
                'use_cod_func' => 'U002',
                'use_username' => 'usuario.rj',
                'use_password' => 'senha123',
                'use_cell' => '(21) 97777-7777',
                'use_uni_id' => $filialRjId,
                'use_rol_id' => $usuarioRoleId,
            ],
            [
                'use_name' => 'Usuário Belo Horizonte',
                'use_email' => 'usuario.bh@exemplo.com',
                'use_cod_func' => 'U003',
                'use_username' => 'usuario.bh',
                'use_password' => 'senha123',
                'use_cell' => '(31) 97777-7777',
                'use_uni_id' => $filialMgId,
                'use_rol_id' => $usuarioRoleId,
            ],
        ];
        
        foreach ($usuarios as $usuario) {
            User::create($usuario);
        }
    }
}