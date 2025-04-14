<?php

namespace App\Http\Livewire\Selfs;

use App\Models\Selfs;
use Livewire\WithPagination;
use Livewire\Component;
use App\Events\Admin\Selfs\Delete as DeleteEvent;


class SelfCheckoutList extends Component
{
    use WithPagination;
    
    protected $paginationTheme = 'bootstrap';
    
    public $search = '';
    public $sortField = 'sel_id';
    public $sortDirection = 'asc';
    public $confirmingDelete = false;
    public $selfToDelete = null;
    
    protected $listeners = [
        'selfSaved' => '$refresh',
        'confirmDelete' => 'confirmDelete',
        'deleteConfirmed' => 'destroy'
    ];
    
    public function mount()
    {
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
    
    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortDirection = 'asc';
        }
        
        $this->sortField = $field;
    }
    
    public function confirmDelete(Selfs $self)
    {
        $this->confirmingDelete = true;
        $this->selfToDelete = $self;
        
        $this->dispatchBrowserEvent('show-delete-modal');
    }
    
    public function cancelDelete()
    {
        $this->confirmingDelete = false;
        $this->selfToDelete = null;
    }
    
    public function toggleStatus(Selfs $self)
    {
        try {
            $self->sel_status = !$self->sel_status;
            $self->save();
            
            event(new \App\Events\Admin\Selfs\Edit($self->sel_id, request()->ip()));
            
            $statusText = $self->sel_status ? 'ativado' : 'desativado';
            
            session()->flash('success', "SelfCheckout {$statusText} com sucesso.");
            
            return redirect()->route('selfs.index');
        } catch (\Exception $e) {
            session()->flash('error', 'Erro ao alterar o status do SelfCheckout.');
            return redirect()->route('selfs.index');
        }
    }
    
    public function destroy()
    {
        try {
            if ($this->selfToDelete) {
                $selfName = $this->selfToDelete->sel_name;
                $selfUniId = $this->selfToDelete->sel_uni_id;
                
                event(new DeleteEvent("NOME SelfCheckout: {$selfName} ID - Unidade: {$selfUniId}", request()->ip()));
                
                $this->selfToDelete->delete();
                
                $this->dispatchBrowserEvent('hide-delete-modal');
                
                session()->flash('success', 'SelfCheckout excluído com sucesso.');
                return redirect()->route('selfs.index');
            }
        } catch (\Exception $e) {
            session()->flash('error', 'Não foi possível excluir o SelfCheckout.');
            return redirect()->route('selfs.index');
        }
        
        $this->confirmingDelete = false;
        $this->selfToDelete = null;
    }
    
    public function render()
    {
        $selfs = Selfs::with('unidade')
            ->when($this->search, function ($query) {
                $search = '%' . $this->search . '%';
                return $query->where('sel_name', 'like', $search)
                    ->orWhere('sel_pdv_ip', 'like', $search)
                    ->orWhere('sel_rtsp_url', 'like', $search);
            })
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate(10);
        
        return view('livewire.admin.selfs.list', [
            'selfs' => $selfs
        ])->extends('adminlte::page')
          ->section('content');
    }
}