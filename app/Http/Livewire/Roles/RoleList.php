<?php

namespace App\Http\Livewire\Roles;

use App\Models\Role;
use Livewire\WithPagination;
use Livewire\Component;
use App\Events\Admin\Role\Delete as DeleteEvent;

class RoleList extends Component
{
    use WithPagination;
    
    protected $paginationTheme = 'bootstrap';
    
    public $search = '';
    public $sortField = 'rol_id';
    public $sortDirection = 'asc';
    public $confirmingDelete = false;
    public $roleToDelete = null;
    
    protected $listeners = [
        'roleSaved' => '$refresh',
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
    
    public function confirmDelete($roleId)
    {
        $role = Role::findOrFail($roleId);
        
        $usuariosAssociados = $role->users()->count();
        
        if ($usuariosAssociados > 0) {
            $errorMessage = 'Não é possível excluir este cargo/função. Existem usuários associados.';
            
            $this->dispatchBrowserEvent('show-error-modal', [
                'message' => $errorMessage
            ]);
            
            return;
        }
        
        $this->roleToDelete = $role;
        $this->confirmingDelete = true;
        $this->dispatchBrowserEvent('show-delete-modal');
    }
    
    public function destroy()
    {
        try {
            if ($this->roleToDelete) {
                $roleName = $this->roleToDelete->rol_name;
                
                event(new DeleteEvent($roleName, request()->ip()));
                
                $this->roleToDelete->delete();
                
                $this->dispatchBrowserEvent('hide-delete-modal');
                
                session()->flash('success', 'Cargo/Função excluído com sucesso.');
                return redirect()->route('roles.index');
            }
        } catch (\Exception $e) {
            session()->flash('error', 'Não foi possível excluir o cargo/função: ' . $e->getMessage());
            return redirect()->route('roles.index');
        }
    }
    
    public function render()
    {
        $roles = Role::when($this->search, function ($query) {
                $search = '%' . $this->search . '%';
                return $query->where('rol_name', 'like', $search);
            })
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate(15);
        
        return view('livewire.admin.roles.list', [
            'roles' => $roles
        ])->extends('adminlte::page')
          ->section('content');
    }
}