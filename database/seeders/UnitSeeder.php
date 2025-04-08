<?php

namespace Database\Seeders;

use App\Models\Unit;
use App\Models\User;
use App\Models\Unidade;
use Illuminate\Database\Seeder;

class UnitSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Obter todos os usuários e unidades
        $users = User::all();
        $unidades = Unidade::all();
        
        // Obter o administrador
        $admin = User::where('use_username', 'admin')->first();
        
        // Associar o administrador a todas as unidades
        foreach ($unidades as $unidade) {
            Unit::create([
                'unit_uni_id' => $unidade->uni_id,
                'unit_use_id' => $admin->use_id,
            ]);
        }
        
        // Para cada gerente, associar a sua unidade principal e uma unidade adicional
        $gerentes = User::where('use_rol_id', 2)->get(); // Supondo que role_id 2 seja Gerente
        
        foreach ($gerentes as $gerente) {
            // Associar à unidade principal (já tem vínculo direto pelo campo use_uni_id)
            // Atribuir uma unidade adicional (diferente da principal)
            $unidadeAdicional = $unidades->where('uni_id', '!=', $gerente->use_uni_id)->random();
            
            Unit::create([
                'unit_uni_id' => $unidadeAdicional->uni_id,
                'unit_use_id' => $gerente->use_id,
            ]);
        }
        
        // Para usuários comuns, associar apenas à sua unidade principal
        // (Eles já têm vínculo direto pelo campo use_uni_id, não necessitando entrada na tabela units)
    }
}