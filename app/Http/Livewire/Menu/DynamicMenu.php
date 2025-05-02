<?php

namespace App\Http\Livewire\Menu;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class DynamicMenu extends Component
{
    public $menuItems = [];
    protected $listeners = [
        'telaDeleted' => 'loadMenu',
        'executeDelete' => 'deleteTela'
    ];

    public function mount()
    {
        $this->loadMenu();
    }

    public function confirmDelete($index)
    {
        $this->dispatchBrowserEvent('show-delete-confirmation', [
            'index' => $index,
        ]);
    }

    public function loadMenu()
    {
        $user = Auth::user();
        $this->menuItems = [];

        if (!$user->ui_preferences || !isset($user->ui_preferences['tela']) || 
            !is_array($user->ui_preferences['tela']) || empty($user->ui_preferences['tela'])) {
            return;
        }

        $telaSubmenu = [];
        foreach ($user->ui_preferences['tela'] as $index => $tela) {
            $url = $this->generateUrl($tela);

            $telaSubmenu[] = [
                'key' => 'monitor_' . ($index + 1),
                'text' => 'Monitor ' . ($index + 1),
                'url' => $url,
                'icon' => 'fas fa-fw fa-desktop',
                'index' => $index
            ];
        }

        if (!empty($telaSubmenu)) {
            $this->menuItems[] = [
                'key' => 'telas',
                'text' => 'Telas',
                'icon' => 'fas fa-fw fa-tv',
                'submenu' => $telaSubmenu
            ];
        }
    }

    public function deleteTela($index)
    {
        $user = Auth::user();

        if (!$user->ui_preferences || !isset($user->ui_preferences['tela']) || 
            !is_array($user->ui_preferences['tela'])) {
            return;
        }

        if (!array_key_exists($index, $user->ui_preferences['tela'])) {
            return;
        }

        $uiPreferences = $user->ui_preferences;
        
        unset($uiPreferences['tela'][$index]);
        $uiPreferences['tela'] = array_values($uiPreferences['tela']);
        
        if (empty($uiPreferences['tela'])) {
            unset($uiPreferences['tela']);
        }

        $user->ui_preferences = $uiPreferences;
        $user->save();

        $this->emit('telaDeleted');
        
        // Opcional: mostrar uma notificação de sucesso
        $this->dispatchBrowserEvent('tela-deleted', ['message' => 'Tela removida com sucesso']);
    }

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

    public function render()
    {
        return view('livewire.menu.dynamic-menu');
    }
}
