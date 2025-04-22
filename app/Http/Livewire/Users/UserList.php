<?php

namespace App\Http\Livewire\Users;

use App\Models\User;
use Livewire\WithPagination;
use Livewire\Component;
use App\Events\Admin\User\Delete as DeleteEvent;

class UserList extends Component
{
    use WithPagination;
    
    protected $paginationTheme = 'bootstrap';
    
    public $search = '';
    public $sortField = 'use_id';
    public $sortDirection = 'asc';
    public $confirmingDelete = false;
    public $userToDelete = null;
    public $userToReset = null;
    
    protected $listeners = [
        'userSaved' => '$refresh',
        'confirmDelete' => 'confirmDelete',
        'deleteConfirmed' => 'destroy'
    ];
    
    public function mount()
    {
        if (session()->has('success')) {
            $this->dispatchBrowserEvent('admin-toastr', [
                'type' => 'success',
                'message' => session('success')
            ]);
        }
        
        if (session()->has('error')) {
            $this->dispatchBrowserEvent('admin-toastr', [
                'type' => 'error',
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
    
    public function confirmDelete($userId)
    {
        $user = User::findOrFail($userId);
        
        $this->userToDelete = $user;
        $this->confirmingDelete = true;
        $this->dispatchBrowserEvent('show-delete-modal');
    }
    
    public function cancelDelete()
    {
        $this->confirmingDelete = false;
        $this->userToDelete = null;
    }
    
    public function destroy()
    {
        try {
            if ($this->userToDelete) {
                $userName = $this->userToDelete->use_username;
                
                event(new DeleteEvent("Username: {$userName}", request()->ip()));

                $this->userToDelete->delete();
                
                $this->dispatchBrowserEvent('hide-delete-modal');
                
                session()->flash('success', 'Usuário excluído com sucesso.');
                return redirect()->route('users.index');
            }
        } catch (\Exception $e) {
            session()->flash('error', 'Não foi possível excluir o usuário: ' . $e->getMessage());
            return redirect()->route('users.index');
        }
        
        $this->confirmingDelete = false;
        $this->userToDelete = null;
    }
    
    public function toggleStatus($userId)
    {
        try {
            $user = User::findOrFail($userId);
            $user->use_active = !$user->use_active;
            $user->save();
            
            event(new \App\Events\Admin\User\Edit($user->use_id, request()->ip()));
            
            $statusText = $user->use_active ? 'ativado' : 'desativado';
            
            session()->flash('success', "Usuário {$statusText} com sucesso.");
            return redirect()->route('users.index');
        } catch (\Exception $e) {
            session()->flash('error', 'Erro ao alterar o status do usuário.');
            return redirect()->route('users.index');
        }
    }
    
    public function resetPassword($userId)
    {
        $user = User::findOrFail($userId);
        $this->userToReset = $user;
        $this->dispatchBrowserEvent('show-reset-password-modal');
    }
    
    public function doResetPassword()
    {
        try {
            if ($this->userToReset) {
                \DB::table('users')
                    ->where('use_id', $this->userToReset->use_id)
                    ->update(['use_password' => bcrypt('senha123')]);
                
                event(new \App\Events\Admin\User\Edit($this->userToReset->use_id, request()->ip()));
                
                $this->dispatchBrowserEvent('hide-reset-password-modal');
                $this->dispatchBrowserEvent('show-password-reseted-modal');
            }
        } catch (\Exception $e) {
            session()->flash('error', 'Erro ao redefinir a senha do usuário: ' . $e->getMessage());
            $this->dispatchBrowserEvent('hide-reset-password-modal');
            return redirect()->route('users.index');
        }
    }
    
    public function resetAnotherPassword()
    {
        $this->dispatchBrowserEvent('hide-password-reseted-modal');
        $this->dispatchBrowserEvent('show-reset-password-modal');
    }
    
    public function render()
    {
        $users = User::when($this->search, function ($query) {
                $search = '%' . $this->search . '%';
                return $query->where('use_name', 'like', $search)
                    ->orWhere('use_username', 'like', $search)
                    ->orWhere('use_email', 'like', $search);
            })
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate(10);
        
        return view('livewire.admin.users.list', [
            'users' => $users
        ])->extends('adminlte::page')
          ->section('content');
    }
}