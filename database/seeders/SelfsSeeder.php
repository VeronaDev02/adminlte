<?php

namespace Database\Seeders;

use App\Models\Selfs;
use App\Models\Unidade;
use Illuminate\Database\Seeder;

class SelfsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Obter IDs de unidades
        $unidades = Unidade::all();
        
        // Para cada unidade, criar alguns PDVs
        foreach ($unidades as $unidade) {
            // Criar 3 PDVs para cada unidade
            for ($i = 1; $i <= 3; $i++) {
                $pdvNumber = str_pad($i, 2, '0', STR_PAD_LEFT);
                $ipBase = '192.168.' . $unidade->uni_id . '.';
                
                Selfs::create([
                    'sel_name' => 'PDV ' . $pdvNumber . ' - ' . $unidade->uni_descricao,
                    'sel_pdv_ip' => $ipBase . (10 + $i),
                    'sel_rtsp_url' => 'rtsp://' . $ipBase . (10 + $i) . ':554/cam' . $i,
                    'sel_status' => true,
                    'sel_uni_id' => $unidade->uni_id,
                ]);
            }
            
            // Adicionar um PDV inativo para testes
            if ($unidade->uni_id === 1) {
                Selfs::create([
                    'sel_name' => 'PDV Inativo - ' . $unidade->uni_descricao,
                    'sel_pdv_ip' => '192.168.' . $unidade->uni_id . '.99',
                    'sel_rtsp_url' => 'rtsp://192.168.' . $unidade->uni_id . '.99:554/cam99',
                    'sel_status' => false,
                    'sel_uni_id' => $unidade->uni_id,
                ]);
            }
        }
    }
}