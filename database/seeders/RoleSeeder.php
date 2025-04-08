<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $roles = [
            ['rol_name' => 'Admin'],
            ['rol_name' => 'Gerente'],
            ['rol_name' => 'Usuário'],
            ['rol_name' => 'Visitante'],
        ];

        foreach ($roles as $role) {
            Role::create($role);
        }
    }
}