<?php

namespace App\Http\Livewire\Unidades;

use App\Models\Unidade;
use App\Models\User;
use App\Models\TipoUnidade;
use Livewire\Component;
use App\Events\Admin\Unidade\Create as CreateEvent;
use App\Events\Admin\Unidade\Edit as EditEvent;

class UnidadeForm extends Component
{
    public $unidadeId;
    public $uni_codigo;
    public $uni_tip_id;
    public $tiposUnidade = [];
    
    public $isEdit = false;
    public $usuarios = [];
    public $usuariosSelecionados = [];
    public $selfsAssociados = [];
    
    protected $listeners = [
        'load' => '$refresh',
    ];
    
    protected function rules()
    {
        $uniqueRuleCodigo = 'required|regex:/^\d+$/|max:3|unique:unidade,uni_codigo';
        
        if ($this->isEdit) {
            $uniqueRuleCodigo .= ',' . $this->unidadeId . ',uni_id';
        }
        
        return [
            'uni_codigo' => $uniqueRuleCodigo,
            'uni_tip_id' => 'required|exists:tipo_unidade,tip_id',
        ];
    }
    
    protected $messages = [
        'uni_codigo.unique' => 'O código da unidade já está sendo utilizado.',
        'uni_codigo.required' => 'O código da unidade é obrigatório.',
        'uni_codigo.max' => 'O código da unidade deve ter no máximo 3 dígitos.',
        'uni_codigo.regex' => 'O código da unidade deve conter apenas números.',
        'uni_tip_id.required' => 'O tipo de unidade é obrigatório.',
        'uni_tip_id.exists' => 'O tipo de unidade selecionado não existe.',
    ];
    
    public function mount($unidade = null)
    {
        $this->tiposUnidade = TipoUnidade::orderBy('tip_nome')->get();
        $this->usuarios = User::orderBy('use_name')->get();
        
        if ($unidade) {
            if (is_numeric($unidade) || is_string($unidade)) {
                $unidade = Unidade::where('uni_id', $unidade)->firstOrFail();
            }
            
            $this->unidadeId = $unidade->uni_id;
            $this->uni_codigo = $unidade->uni_codigo;
            $this->uni_tip_id = $unidade->uni_tip_id;
            
            $this->usuariosSelecionados = $unidade->users()->pluck('use_id')->toArray() ?? [];
            
            $this->selfsAssociados = $unidade->selfs ?? collect([]);
            
            $this->isEdit = true;
        }
        $this->dispatchBrowserEvent('contentChanged');
    }
    
    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
        if ($propertyName === 'uni_tip_id') {
            $this->dispatchBrowserEvent('contentChanged');
        }
    }
    
    public function adicionarUsuario($userId)
    {
        if (!in_array($userId, $this->usuariosSelecionados)) {
            $this->usuariosSelecionados[] = $userId;
        }
        $this->dispatchBrowserEvent('contentChanged');
    }
    
    public function removerUsuario($userId)
    {
        $this->usuariosSelecionados = array_filter($this->usuariosSelecionados, function($id) use ($userId) {
            return $id != $userId;
        });
        
        $this->dispatchBrowserEvent('contentChanged');
    }
    
    public function adicionarTodosUsuarios()
    {
        $this->usuariosSelecionados = $this->usuarios->pluck('use_id')->toArray();
        $this->dispatchBrowserEvent('contentChanged');
    }
    
    public function removerTodosUsuarios()
    {
        $this->usuariosSelecionados = [];
        $this->dispatchBrowserEvent('contentChanged');
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
        $this->dispatchBrowserEvent('contentChanged');
        
        return view('livewire.admin.unidades.form', [
            'title' => $title
        ]);
    }
}