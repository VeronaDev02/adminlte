<?php

namespace App\Http\Livewire\Unidades;

use App\Models\Unidade;
use App\Models\TipoUnidade;
use Livewire\WithPagination;
use Livewire\Component;
use App\Events\Admin\Unidade\Delete as DeleteEvent;

class UnidadeList extends Component
{
    use WithPagination;
    
    protected $paginationTheme = 'bootstrap';
    
    public $search = '';
    public $sortField = 'uni_id';
    public $sortDirection = 'asc';
    public $confirmingDelete = false;
    public $unidadeToDelete = null;
    
    protected $listeners = [
        'unidadeSaved' => '$refresh',
        'confirmDelete' => 'confirmDelete',
        'deleteConfirmed' => 'destroy',
        'refresh' => '$refresh',
        'searchUpdated' => 'updateSearch'
    ];
    
    public function mount()
    {
    }
    
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
                return redirect()->route('unidades.index');
            }
        } catch (\Exception $e) {
            session()->flash('error', 'Não foi possível excluir a unidade: ' . $e->getMessage());
            return redirect()->route('unidades.index');
        }
    }
    
    public function render()
    {
        $unidades = Unidade::with('tipoUnidade')
            ->when($this->search, function ($query) {
                $search = '%' . $this->search . '%';
                return $query->where('uni_codigo', 'like', $search)
                    ->orWhere('uni_nome', 'like', $search) 
                    ->orWhereHas('tipoUnidade', function($q) use ($search) {
                        $q->where('tip_nome', 'like', $search);
                    });
            })
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate(10);
        
        return view('livewire.admin.unidades.list', [
            'unidades' => $unidades
        ]);
    }
}