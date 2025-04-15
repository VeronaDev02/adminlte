<?php

namespace App\Filters;

use App\Models\Unidade;
use Illuminate\Support\Facades\Log;
use JeroenNoten\LaravelAdminLte\Menu\Filters\FilterInterface;
use Illuminate\Support\Facades\Auth;

class MenuFilter implements FilterInterface
{
    public function transform($item)
    {
        // Verifica se este é o item marcado para ter um submenu dinâmico
        if (isset($item['dynamic_submenu']) && $item['dynamic_submenu'] === true) {
            try {
                // Obtém todas as unidades diretamente (sem cache por enquanto)
                $unidades = Unidade::orderBy('uni_nome')->get();
                
                // Prepara o submenu
                $item['submenu'] = [];
                
                foreach ($unidades as $unidade) {
                    $item['submenu'][] = [
                        'text' => $unidade->uni_nome,
                        'url' => '/unidades/' . $unidade->uni_id,
                        'icon' => 'far fa-circle',
                        'icon_color' => $unidade->uni_cor,
                    ];
                }
                
                // Remove o marcador dinâmico
                unset($item['dynamic_submenu']);
                
                // Garante que o item foi processado corretamente
                if (empty($item['submenu'])) {
                    Log::warning('Nenhuma unidade encontrada para o menu dinâmico');
                }
            } catch (\Exception $e) {
                Log::error('Erro ao carregar o menu dinâmico: ' . $e->getMessage());
                // Em caso de erro, remove o marcador e continua
                unset($item['dynamic_submenu']);
            }
        }
        
        return $item;
    }
}
