<?php

namespace App\Http\Livewire\Unidades;

use App\Models\Unidade;
use App\Models\User;
use App\Models\TipoUnidade;
use Livewire\Component;
use App\Events\Admin\Unidade\Create as CreateEvent;
use App\Events\Admin\Unidade\Edit as EditEvent;

class UnidadeFormPorTipo extends Component
{
    public $unidadeId;
    public $uni_codigo;
    public $uni_tip_id;
    public $tipoUnidadeCodigo; // Código do tipo de unidade
    public $tipoUnidade; // Objeto do tipo de unidade
    
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
        ];
    }
    
    protected $messages = [
        'uni_codigo.unique' => 'O código da unidade já está sendo utilizado.',
        'uni_codigo.required' => 'O código da unidade é obrigatório.',
        'uni_codigo.max' => 'O código da unidade deve ter no máximo 3 dígitos.',
        'uni_codigo.regex' => 'O código da unidade deve conter apenas números.',
    ];
    
    public function mount($tipoCodigo, $unidade = null)
    {
        $this->tipoUnidadeCodigo = $tipoCodigo;
        $this->tipoUnidade = TipoUnidade::where('tip_codigo', $tipoCodigo)->firstOrFail();
        $this->uni_tip_id = $this->tipoUnidade->tip_id;
        
        $this->usuarios = User::orderBy('use_name')->get();
        
        if ($unidade) {
            // Se $unidade for um ID (número) ou string, busque o objeto
            if (is_numeric($unidade) || is_string($unidade)) {
                $unidade = Unidade::where('uni_id', $unidade)
                    ->where('uni_tip_id', $this->tipoUnidade->tip_id)
                    ->firstOrFail();
            }
            
            $this->unidadeId = $unidade->uni_id;
            $this->uni_codigo = $unidade->uni_codigo;
            
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
        $validatedData['uni_tip_id'] = $this->uni_tip_id;
        
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
            
            return redirect()->route('tipo-unidade.unidades', ['codigo' => $this->tipoUnidadeCodigo]);
        } catch (\Exception $e) {
            session()->flash('error', 'Ocorreu um erro: ' . $e->getMessage());
            return redirect()->back();
        }
    }
    
    public function render()
    {
        $title = $this->isEdit ? 'Editar Unidade' : 'Criar Nova Unidade';
        $subtitle = "Tipo: {$this->tipoUnidade->tip_nome}";
        
        return view('livewire.admin.unidades.form-por-tipo', [
            'title' => $title,
            'subtitle' => $subtitle
        ]);
    }
}