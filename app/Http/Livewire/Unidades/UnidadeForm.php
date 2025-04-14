<?php

namespace App\Http\Livewire\Unidades;

use App\Models\Unidade;
use App\Models\User;
use Livewire\Component;
use App\Events\Admin\Unidade\Create as CreateEvent;
use App\Events\Admin\Unidade\Edit as EditEvent;

class UnidadeForm extends Component
{
    public $unidadeId;
    public $uni_codigo;
    public $uni_descricao;
    public $uni_cidade;
    public $uni_uf;
    
    public $isEdit = false;
    public $usuarios = [];
    public $usuariosSelecionados = [];
    public $selfsAssociados = [];
    
    protected $listeners = [
        'load' => '$refresh',
    ];
    
    protected function rules()
    {
        $uniqueRuleCodigo = 'required|regex:"^\d+$"|max:3|unique:unidade,uni_codigo';
        
        if ($this->isEdit) {
            $uniqueRuleCodigo .= ',' . $this->unidadeId . ',uni_id';
        }
        
        return [
            'uni_codigo' => $uniqueRuleCodigo,
            'uni_descricao' => 'required|string|max:255',
            'uni_cidade' => 'required|string|max:100',
            'uni_uf' => 'required|string|size:2',
        ];
    }
    
    protected $messages = [
        'uni_codigo.unique' => 'O código da unidade já está sendo utilizado.',
        'uni_codigo.required' => 'O código da unidade é obrigatório.',
        'uni_codigo.max' => 'O código da unidade deve ter no máximo 3 dígitos.',
        'uni_codigo.regex' => 'O código da unidade deve conter apenas números.',
        'uni_descricao.required' => 'O nome da unidade é obrigatório.',
        'uni_descricao.max' => 'O nome da unidade não pode ter mais de 255 caracteres.',
        'uni_cidade.required' => 'A cidade é obrigatória.',
        'uni_cidade.max' => 'A cidade não pode ter mais de 100 caracteres.',
        'uni_uf.required' => 'A UF é obrigatória.',
        'uni_uf.size' => 'A UF deve ter 2 caracteres.',
    ];
    
    public function mount($unidade = null)
    {
        $this->usuarios = User::orderBy('use_name')->get();
        
        if ($unidade) {
            // Se $unidade for um ID (número) ou string, busque o objeto
            if (is_numeric($unidade) || is_string($unidade)) {
                $unidade = Unidade::findOrFail($unidade);
            }
            
            $this->unidadeId = $unidade->uni_id;
            $this->uni_codigo = $unidade->uni_codigo;
            $this->uni_descricao = $unidade->uni_descricao;
            $this->uni_cidade = $unidade->uni_cidade;
            $this->uni_uf = $unidade->uni_uf;
            
            // Buscar usuários já associados à unidade
            $this->usuariosSelecionados = $unidade->users()->pluck('use_id')->toArray() ?? [];
            
            // Buscar SelfCheckouts associados
            $this->selfsAssociados = $unidade->selfs ?? collect([]);
            
            $this->isEdit = true;
        }
    }
    
    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
    }
    
    public function adicionarUsuario($userId)
    {
        if (!in_array($userId, $this->usuariosSelecionados)) {
            $this->usuariosSelecionados[] = $userId;
        }
    }
    
    public function removerUsuario($userId)
    {
        $this->usuariosSelecionados = array_filter($this->usuariosSelecionados, function($id) use ($userId) {
            return $id != $userId;
        });
    }
    
    public function save()
    {
        $validatedData = $this->validate();
        
        try {
            if ($this->isEdit) {
                $unidade = Unidade::findOrFail($this->unidadeId);
                $unidade->update($validatedData);
                
                event(new EditEvent($unidade->uni_id, request()->ip()));
                
                $unidade->users()->sync($this->usuariosSelecionados);
                
                session()->flash('success', 'Unidade atualizada com sucesso.');
            } else {
                $unidade = Unidade::create($validatedData);
                
                $unidade->users()->sync($this->usuariosSelecionados);
                
                event(new CreateEvent($unidade->uni_id, request()->ip()));
                
                session()->flash('success', 'Unidade criada com sucesso.');
            }
            
            return redirect()->route('unidades.index');
        } catch (\Exception $e) {
            session()->flash('error', 'Ocorreu um erro: ' . $e->getMessage());
            return redirect()->back();
        }
    }
    
    public function render()
    {
        $title = $this->isEdit ? 'Editar Unidade' : 'Criar Nova Unidade';
        
        return view('livewire.admin.unidades.form', [
            'title' => $title
        ])->extends('adminlte::page')
          ->section('content');
    }
}