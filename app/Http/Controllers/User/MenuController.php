<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class MenuController extends Controller
{
    private function generateUrl($tela)
    {
        $pdvs = [];
        foreach ($tela['selectedPdvs'] as $key => $value) {
            if (!empty($value)) {
                $pdvs["pdv[{$key}]"] = $value;
            }
        }

        return url(route('selfs.monitor', array_merge([
            'quadrants' => $tela['quadrants'],
            'cols' => $tela['columns'],
            'rows' => $tela['rows']
        ], $pdvs)));
    }

    public function generateDynamicMenu()
    {
        Log::info('Gerando menu dinâmico');

        $user = Auth::user();

        Log::info('Usuário autenticado', [
            'id' => $user->id,
            'ui_preferences' => $user->ui_preferences
        ]);

        if (!$user->ui_preferences) {
            Log::warning('Sem preferências de UI');
            return response()->json(['menu' => []]);
        }

        $uiPreferences = $user->ui_preferences;

        if (!isset($uiPreferences['tela']) || !is_array($uiPreferences['tela']) || empty($uiPreferences['tela'])) {
            Log::warning('Sem configuração de telas');
            return response()->json(['menu' => []]);
        }

        $telaSubmenu = [];
        foreach ($uiPreferences['tela'] as $index => $tela) {
            $url = $this->generateUrl($tela);

            $telaSubmenu[] = [
                'key' => 'monitor_' . ($index + 1),
                'text' => 'Monitor ' . ($index + 1),
                'url' => $url,
                'icon' => 'fas fa-fw fa-desktop',
                'delete_url' => route('menu.deleteTela', ['index' => $index])
            ];
        }

        $menu = [
            [
                'key' => 'telas',
                'text' => 'Telas',
                'icon' => 'fas fa-fw fa-tv',
                'submenu' => $telaSubmenu
            ]
        ];

        return response()->json(['menu' => $menu]);
    }

    public function deleteTela($index)
    {
        $user = Auth::user();

        if (!$user->ui_preferences) {
            return response()->json(['message' => 'Nenhuma preferência encontrada.'], 404);
        }

        $uiPreferences = $user->ui_preferences;

        if (!isset($uiPreferences['tela']) || !is_array($uiPreferences['tela'])) {
            return response()->json(['message' => 'Nenhuma tela configurada.'], 404);
        }

        if (!array_key_exists($index, $uiPreferences['tela'])) {
            return response()->json(['message' => 'Tela não encontrada.'], 404);
        }

        $telaToDelete = $uiPreferences['tela'][$index];

        unset($uiPreferences['tela'][$index]);

        $uiPreferences['tela'] = array_values($uiPreferences['tela']);

        if (empty($uiPreferences['tela'])) {
            unset($uiPreferences['tela']);
        }

        $user->ui_preferences = $uiPreferences;
        $user->save();

        // Log::info('Tela removida do usuário', [
        //     'user_id' => $user->id,
        //     'tela_index' => $index,
        //     'tela_removed' => $telaToDelete
        // ]);

        return response()->json(['message' => 'Tela removida com sucesso.']);
    }
}
