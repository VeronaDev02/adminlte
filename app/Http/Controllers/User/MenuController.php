<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class MenuController extends Controller
{

    public function generateDynamicMenu()
    {
        // Log de início do método pra testar
        Log::info('Gerando menu dinâmico');

        // Recupera o usuário atualmente autenticado
        $user = Auth::user();

        // Log do usuário autenticado
        Log::info('Usuário autenticado', [
            'id' => $user->id,
            'ui_preferences' => $user->ui_preferences
        ]);

        // Verifica se o usuário tem preferências de UI
        if (!$user->ui_preferences) {
            Log::warning('Sem preferências de UI');
            return response()->json(['menu' => null]);  // Retorna null caso não tenha preferências
        }

        // Verifica o tipo de ui_preferences
        $uiPreferences = is_string($user->ui_preferences) 
            ? json_decode($user->ui_preferences, true) 
            : $user->ui_preferences;

        // Log das preferências
        Log::info('Preferências processadas', [
            'preferencias' => $uiPreferences
        ]);

        // Verifica se existe a chave 'tela' nas preferências
        if (!isset($uiPreferences['tela']) || !is_array($uiPreferences['tela'])) {
            Log::warning('Sem configuração de telas');
            return response()->json(['menu' => null]);  // Retorna null caso não tenha configuração de telas
        }

        // Cria o submenu de Telas baseado nas preferências
        $telaSubmenu = [];
        foreach ($uiPreferences['tela'] as $index => $tela) {
            // Log de cada configuração de tela
            Log::info('Processando tela', [
                'index' => $index,
                'tela' => $tela
            ]);

            // Prepara os PDVs para a URL
            $pdvs = [];
            foreach ($tela['selectedPdvs'] as $key => $value) {
                $pdvs["pdv[{$key}]"] = $value;
            }

            // Constrói a URL dinâmica baseada nas configurações da tela
            $url = url(route('selfs.monitor', array_merge([
                'quadrants' => $tela['quadrants'],
                'cols' => $tela['columns'],
                'rows' => $tela['rows']
            ], $pdvs)));

            $telaSubmenu[] = [
                'key' => 'monitor_' . ($index + 1),
                'text' => 'Monitor ' . ($index + 1),
                'url' => $url,
                'icon' => 'fas fa-fw fa-desktop'
            ];
        }

        // Log do submenu de telas
        Log::info('Submenu de telas gerado', [
            'submenu' => $telaSubmenu
        ]);

        // Adiciona o submenu de Telas ao menu existente
        $menu = [
            [
                'key' => 'telas',
                'text' => 'Telas',
                'icon' => 'fas fa-fw fa-tv',
                'submenu' => $telaSubmenu
            ]
        ];

        // Log do menu final
        Log::info('Menu final gerado', [
            'menu' => $menu
        ]);

        return response()->json(['menu' => $menu]);
    }
}