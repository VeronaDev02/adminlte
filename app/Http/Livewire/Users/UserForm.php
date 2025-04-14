<?php

namespace App\Http\Livewire\Users;

use App\Models\User;
use App\Models\Role;
use App\Models\Unidade;
use Livewire\Component;
use App\Events\Admin\User\Create as CreateEvent;
use App\Events\Admin\User\Edit as EditEvent;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Admin\UserController;
use Illuminate\Http\Request;

class UserForm extends Component
{
    protected $userController;

    public $userId;
    public $use_name;
    public $use_username;
    public $use_email;
    public $use_password;
    public $use_cod_func;
    public $use_rol_id;
    public $use_cell;
    public $use_active = true;
    public $use_login_ativo = true;
    
    public $isEdit = false;
    public $roles = [];
    public $unidades = [];
    public $usuariosSelecionados = [];
    
    protected $listeners = [
        'load' => '$refresh',
    ];
    
    protected function rules()
    {
        $rules = [
            'use_name' => 'required|string|max:255',
            'use_username' => 'required|string|max:50|unique:users,use_username',
            'use_email' => 'nullable|string|email|max:255|unique:users,use_email',
            'use_cod_func' => 'required|string|max:50|unique:users,use_cod_func',
            'use_rol_id' => 'required|exists:role,rol_id',
        ];
        
        // Alterar regras para edição
        if ($this->isEdit) {
            $rules['use_username'] .= ',' . $this->userId . ',use_id';
            $rules['use_email'] .= ',' . $this->userId . ',use_id';
            $rules['use_cod_func'] .= ',' . $this->userId . ',use_id';
            
            // Senha opcional na edição
            $rules['use_password'] = 'nullable|string|min:6';
        } else {
            // Senha obrigatória na criação
            $rules['use_password'] = 'required|string|min:6';
        }
        
        return $rules;
    }
    
    protected $messages = [
        'use_name.required' => 'O nome do usuário é obrigatório.',
        'use_username.required' => 'O nome de usuário é obrigatório.',
        'use_username.unique' => 'Este nome de usuário já está em uso.',
        'use_email.email' => 'O email informado não é válido.',
        'use_email.unique' => 'Este email já está em uso.',
        'use_cod_func.required' => 'O código do funcionário é obrigatório.',
        'use_cod_func.unique' => 'Este código de funcionário já está em uso.',
        'use_rol_id.required' => 'A função é obrigatória.',
        'use_password.required' => 'A senha é obrigatória.',
        'use_password.min' => 'A senha deve ter no mínimo 6 caracteres.',
    ];
    
    public function mount($user = null, UserController $userController)
    {
        $this->userController = $userController;
        $this->roles = Role::all();
        $this->unidades = Unidade::all();
        
        if ($user) {
            // Se $user for um ID (número) ou string, busque o objeto
            if (is_numeric($user) || is_string($user)) {
                $user = User::findOrFail($user);
            }
            
            $this->userId = $user->use_id;
            $this->use_name = $user->use_name;
            $this->use_username = $user->use_username;
            $this->use_email = $user->use_email;
            $this->use_cod_func = $user->use_cod_func;
            $this->use_rol_id = $user->use_rol_id;
            $this->use_cell = $user->use_cell;
            $this->use_active = (bool) $user->use_active;
            $this->use_login_ativo = (bool) $user->use_login_ativo;
            
            // Buscar unidades já associadas ao usuário
            $this->usuariosSelecionados = $user->unidades()->pluck('uni_id')->toArray();
            
            $this->isEdit = true;
        }
    }
    
    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
    }
    
    // Este método é chamado automaticamente pelo Livewire
    public function updatedUseName()
    {
        $this->use_username = $this->gerarUsername($this->use_name);
    }
    
    // Novo método público que pode ser chamado via JavaScript
    public function gerarUsernameAutomatico()
    {
        if ($this->use_name) {
            $this->use_username = $this->gerarUsername($this->use_name);
        }
    }
    
    private function gerarUsername($nome)
    {
        // Remover acentos e converter para minúsculas
        $nome = Str::of($nome)->ascii()->lower();
        
        // Dividir o nome em partes
        $partes = explode(' ', $nome);
        
        // Se tiver mais de uma parte, usar primeiro_ultimo
        if (count($partes) > 1) {
            $username = $partes[0] . '_' . $partes[count($partes) - 1];
        } else {
            // Se for apenas uma palavra, usar ela mesma
            $username = $partes[0];
        }
        
        // Substituir caracteres especiais por underscores
        $username = preg_replace('/[^a-z0-9_]/', '', $username);
        
        return $username;
    }
    
    public function adicionarUnidade($unidadeId)
    {
        if (!in_array($unidadeId, $this->usuariosSelecionados)) {
            $this->usuariosSelecionados[] = $unidadeId;
        }
    }
    
    public function removerUnidade($unidadeId)
    {
        $this->usuariosSelecionados = array_filter($this->usuariosSelecionados, function($id) use ($unidadeId) {
            return $id != $unidadeId;
        });
    }
    
    public function save()
    {
        $validatedData = $this->validate();
        
        try {
            if ($this->isEdit) {
                $user = User::findOrFail($this->userId);
                
                // Remover campos de senha se estiverem vazios
                if (empty($validatedData['use_password'])) {
                    unset($validatedData['use_password']);
                }
                
                // Garantir que os valores booleanos sejam tratados corretamente
                $validatedData['use_active'] = (bool) $this->use_active;
                $validatedData['use_login_ativo'] = (bool) $this->use_login_ativo;
                
                $user->update($validatedData);
                
                event(new EditEvent($user->use_id, request()->ip()));
                
                // Atualizar unidades associadas
                $user->unidades()->sync($this->usuariosSelecionados);
                
                session()->flash('success', 'Usuário atualizado com sucesso.');
            } else {
                // Garantir que os valores booleanos sejam tratados corretamente
                $validatedData['use_active'] = (bool) $this->use_active;
                $validatedData['use_login_ativo'] = (bool) $this->use_login_ativo;
                
                $user = User::create($validatedData);
                
                // Vincular unidades para criar usuário
                $user->unidades()->sync($this->usuariosSelecionados);
                
                event(new CreateEvent($user->use_id, request()->ip()));
                
                session()->flash('success', 'Usuário criado com sucesso.');
            }
            
            // Redirecionar após salvar
            return redirect()->route('users.index');
        } catch (\Exception $e) {
            session()->flash('error', 'Ocorreu um erro: ' . $e->getMessage());
            return redirect()->back();
        }
    }
    
    public function render()
    {
        $title = $this->isEdit ? 'Editar Usuário' : 'Criar Novo Usuário';
        
        return view('livewire.admin.users.form', [
            'title' => $title
        ])->extends('adminlte::page')
          ->section('content');
    }
    public function buscarFuncionario($codFuncionario = null)
    {
        $this->userController = app(UserController::class);

        if (!$codFuncionario) {
            $codFuncionario = $this->use_cod_func;
        }
        
        if (empty($codFuncionario)) {
            $this->dispatchBrowserEvent('admin-toastr', [
                'type' => 'error',
                'message' => 'Por favor, insira um código de funcionário'
            ]);
            return;
        }
        
        try {
            // Criar um request simulado para passar ao controller
            $request = new Request();
            $request->merge(['use_cod_func' => $codFuncionario]);
            
            // Chamar o método do controller diretamente
            $response = $this->userController->getFuncionario($request);
            
            // Converter a resposta JSON para array
            $data = json_decode($response->getContent(), true);
            
            if (isset($data['name'])) {
                // Atualizar o nome
                $this->use_name = $data['name'];
                
                // Atualizar o nome de usuário baseado no nome
                $this->use_username = $this->gerarUsername($this->use_name);
                
                // Emitir evento para atualizar a interface
                $this->emit('load');
                
                $this->dispatchBrowserEvent('admin-toastr', [
                    'type' => 'success',
                    'message' => 'Funcionário encontrado com sucesso!'
                ]);
            } else if (isset($data['error'])) {
                $this->dispatchBrowserEvent('admin-toastr', [
                    'type' => 'error',
                    'message' => $data['error']
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Erro ao buscar funcionário: ' . $e->getMessage());
            
            $this->dispatchBrowserEvent('admin-toastr', [
                'type' => 'error',
                'message' => 'Ocorreu um erro ao buscar o funcionário: ' . $e->getMessage()
            ]);
        }
    }
}