<?php

namespace App\Http\Livewire\Roles;

use App\Models\Role;
use App\Models\User;
use Livewire\Component;
use App\Events\Admin\Role\Create as CreateEvent;
use App\Events\Admin\Role\Edit as EditEvent;

class RoleForm extends Component
{
    public $roleId;
    public $rol_name;
    
    public $isEdit = false;
    public $usuarios = [];
    
    protected $listeners = [
        'load' => '$refresh',
    ];
    
    protected function rules()
    {
        $uniqueRuleName = 'required|string|max:255|unique:role,rol_name';
        
        if ($this->isEdit) {
            $uniqueRuleName .= ',' . $this->roleId . ',rol_id';
        }
        
        return [
            'rol_name' => $uniqueRuleName,
        ];
    }
    
    protected $messages = [
        'rol_name.unique' => 'Este nome de cargo/função já está sendo utilizado.',
        'rol_name.required' => 'O nome do cargo/função é obrigatório.',
        'rol_name.max' => 'O nome do cargo/função não pode ter mais de 255 caracteres.',
    ];
    
    public function mount($role = null)
    {
        if ($role) {
            if (is_numeric($role) || is_string($role)) {
                $role = Role::findOrFail($role);
            }
            
            $this->roleId = $role->rol_id;
            $this->rol_name = $role->rol_name;
            
            $this->isEdit = true;
            
            // Buscar usuários associados ao role
            $this->usuarios = $role->users()->orderBy('use_name')->get();
        } else {
            $this->usuarios = collect();
        }
    }
    
    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
    }
    
    public function save()
    {
        $validatedData = $this->validate();
        
        try {
            if ($this->isEdit) {
                $role = Role::findOrFail($this->roleId);
                $role->update($validatedData);
                
                event(new EditEvent($role->rol_id, request()->ip()));
                
                session()->flash('success', 'Cargo/Função atualizado com sucesso.');
            } else {
                $role = Role::create($validatedData);
                
                event(new CreateEvent($role->rol_id, request()->ip()));
                
                session()->flash('success', 'Cargo/Função criado com sucesso.');
            }
            
            return redirect()->route('roles.index');
        } catch (\Exception $e) {
            session()->flash('error', 'Ocorreu um erro: ' . $e->getMessage());
            return redirect()->back();
        }
    }
    
    public function render()
    {
        $title = $this->isEdit ? 'Editar Cargo/Função' : 'Criar Novo Cargo/Função';
        
        return view('livewire.admin.roles.form', [
            'title' => $title
        ])->extends('adminlte::page')
          ->section('content');
    }
}