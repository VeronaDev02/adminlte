<?php

namespace App\Http\Livewire\Selfs;

use Livewire\Component;
use App\Services\Selfs\GridLayoutService;
use Illuminate\Support\Facades\Auth;

class SelfConfig extends Component
{
    public $pdvs = [];
    public $selectedQuadrants = 0;
    public $selectedColumns = 0;
    public $selectedRows = 0;
    public $selectedPdvs = [];
    public $layoutPreviewHtml = '';
    public $preferenceName = '';

    protected $gridLayoutService;

    public function boot(GridLayoutService $gridLayoutService)
    {
        $this->gridLayoutService = $gridLayoutService;
    }

    public function mount()
    {
        $user = Auth::user();
        
        // Obter todos os selfs do usuário através de suas unidades (sem usar o repository)
        $selfsList = $user->unidades()
            ->with('selfs')
            ->get()
            ->flatMap(function($unidade) {
                return $unidade->selfs()->active()->get();
            });
        
        // Preparar a lista de PDVs
        $this->pdvs = $selfsList->map(function ($self) {
            return [
                'id' => $self->sel_id,
                'nome' => $self->sel_name,
                'pdvIp' => $self->sel_pdv_ip,
                'pdvCodigo' => $self->sel_pdv_codigo,
                'rtspUrl' => $self->sel_rtsp_path,
            ];
        })->toArray();
        
        $this->selectedPdvs = array_fill(1, 16, null);
    }

    public function getQuadrantOptions()
    {
        return $this->gridLayoutService->generateQuadrantOptions(count($this->pdvs));
    }

    public function updatedSelectedQuadrants()
    {
        $this->selectedColumns = 0;
        $this->selectedRows = 0;
        $this->selectedPdvs = [];
        $this->layoutPreviewHtml = '';
    }

    public function updatedSelectedColumns()
    {
        if ($this->selectedQuadrants && $this->selectedColumns) {
            if ($this->gridLayoutService->validateGridConfiguration($this->selectedQuadrants, $this->selectedColumns)) {
                $this->selectedRows = $this->gridLayoutService->calculateRows($this->selectedQuadrants, $this->selectedColumns);
                $this->layoutPreviewHtml = $this->gridLayoutService->generateLayoutPreviewHtml(
                    $this->selectedQuadrants,
                    $this->selectedColumns,
                    $this->selectedRows
                );
            } else {
                $this->selectedRows = 0;
                $this->layoutPreviewHtml = '';
                session()->flash('error', 'Configuração de grid inválida');
            }
        }
    }

    public function isConfigurationValid()
    {
        $selectedPdvs = array_filter($this->selectedPdvs);
        return count($selectedPdvs) === (int)$this->selectedQuadrants;
    }

    public function applyConfiguration()
    {
        $selectedPdvs = array_filter($this->selectedPdvs);

        if (count($selectedPdvs) !== (int)$this->selectedQuadrants) {
            session()->flash('error', "Selecione PDVs para todas as {$this->selectedQuadrants} telas");
            return;
        }

        $preferences = [
            'quadrants' => $this->selectedQuadrants,
            'columns' => $this->selectedColumns,
            'rows' => $this->selectedRows,
            'selectedPdvs' => $this->selectedPdvs,
            'display_name' => !empty($this->preferenceName) ? $this->preferenceName : 'Monitor ' . (count(auth()->user()->ui_preferences['tela'] ?? []) + 1)
        ];

        $user = auth()->user();
        $currentPreferences = $user->ui_preferences ?? [];

        if (!isset($currentPreferences['tela'])) {
            $currentPreferences['tela'] = [];
        }

        $currentPreferences['tela'][] = $preferences;
        $user->ui_preferences = $currentPreferences;
        $user->save();

        session()->flash('success', 'Preferências salvas com sucesso');

        $urlParams = [
            'quadrants' => $this->selectedQuadrants,
            'cols' => $this->selectedColumns,
            'rows' => $this->selectedRows
        ];

        foreach ($selectedPdvs as $index => $pdvId) {
            $urlParams["pdv[{$index}]"] = $pdvId;
        }

        return redirect()->route('selfcheckout.index', $urlParams);
    }

    public function render()
    {
        return view('livewire.selfs.self-config', [
            'quadrantOptions' => $this->getQuadrantOptions(),
            'layoutPreviewHtml' => $this->layoutPreviewHtml,
        ]);
    }
}