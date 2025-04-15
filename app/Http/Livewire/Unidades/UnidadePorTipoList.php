<?php

namespace App\Http\Livewire\Unidades;

use App\Models\Unidade;
use App\Models\TipoUnidade;
use Livewire\WithPagination;
use Livewire\Component;
use App\Events\Admin\Unidade\Delete as DeleteEvent;

class UnidadePorTipoList extends Component
{
    use WithPagination;
    
    protected $paginationTheme = 'bootstrap';
    
    public $search = '';
    public $sortField = 'uni_id';
    public $sortDirection = 'asc';
    public $confirmingDelete = false;
    public $unidadeToDelete = null;
    public $tipoUnidadeCodigo; // Código do tipo de unidade
    public $tipoUnidade; // Objeto do tipo de unidade
    
    protected $listeners = [
        'unidadeSaved' => '$refresh',
        'confirmDelete' => 'confirmDelete',
        'deleteConfirmed' => 'destroy',
        'refresh' => '$refresh',
        'searchUpdated' => 'updateSearch' // Novo listener para pesquisa
    ];
    
    public function mount($tipoCodigo)
    {
        $this->tipoUnidadeCodigo = $tipoCodigo;
        $this->tipoUnidade = TipoUnidade::where('tip_codigo', $tipoCodigo)->first();
        
        if (!$this->tipoUnidade) {
            session()->flash('error', 'Tipo de unidade não encontrado');
            return redirect()->route('unidades.index');
        }
        
        if (session()->has('success')) {
            $this->dispatchBrowserEvent('toastr:success', [
                'message' => session('success')
            ]);
        }
        
        if (session()->has('error')) {
            $this->dispatchBrowserEvent('toastr:error', [
                'message' => session('error')
            ]);
        }
    }
    
    // Método para atualizar a pesquisa a partir do JavaScript
    public function updateSearch($value)
    {
        $this->search = $value;
    }
    
    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortDirection = 'asc';
        }
        
        $this->sortField = $field;
    }
    
    public function confirmDelete($unidadeId)
    {
        $unidade = Unidade::findOrFail($unidadeId);
        
        $usuariosAssociados = $unidade->users()->count();
        $selfsAssociados = $unidade->selfs()->count();
        
        if ($usuariosAssociados > 0 || $selfsAssociados > 0) {
            $errorMessage = 'Não é possível excluir esta unidade. ';
            
            if ($usuariosAssociados > 0 && $selfsAssociados > 0) {
                $errorMessage .= 'Existem usuários e SelfCheckouts associados.';
            } elseif ($usuariosAssociados > 0) {
                $errorMessage .= 'Existem usuários associados a esta unidade.';
            } else {
                $errorMessage .= 'Existem SelfCheckouts associados a esta unidade.';
            }
            
            $this->dispatchBrowserEvent('toastr:error', [
                'message' => $errorMessage
            ]);
            
            $this->dispatchBrowserEvent('show-error-modal', [
                'message' => $errorMessage
            ]);
            
            return;
        }
        
        $this->unidadeToDelete = $unidade;
        $this->confirmingDelete = true;
        $this->dispatchBrowserEvent('show-delete-modal');
    }
    
    public function destroy()
    {
        try {
            if ($this->unidadeToDelete) {
                $unidadeCodigo = $this->unidadeToDelete->uni_codigo;
                
                event(new DeleteEvent("Codigo Unidade: {$unidadeCodigo}", request()->ip()));
                
                $this->unidadeToDelete->delete();
                
                $this->dispatchBrowserEvent('hide-delete-modal');
                
                session()->flash('success', 'Unidade excluída com sucesso.');
                return redirect()->route('tipo-unidade.unidades', ['codigo' => $this->tipoUnidadeCodigo]);
            }
        } catch (\Exception $e) {
            session()->flash('error', 'Não foi possível excluir a unidade: ' . $e->getMessage());
            return redirect()->route('tipo-unidade.unidades', ['codigo' => $this->tipoUnidadeCodigo]);
        }
    }
    
    public function render()
    {
        $unidades = Unidade::with('tipoUnidade')
            ->where('uni_tip_id', $this->tipoUnidade->tip_id)
            ->when($this->search, function ($query) {
                $search = '%' . $this->search . '%';
                return $query->where('uni_codigo', 'like', $search);
            })
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate(10);
        
        return view('livewire.admin.unidades.tipo-list', [
            'unidades' => $unidades,
            'tipoUnidade' => $this->tipoUnidade
        ]);
    }
}