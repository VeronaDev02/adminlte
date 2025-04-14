<?php

namespace App\Http\Livewire\Selfs;

use App\Models\Selfs;
use App\Models\Unidade;
use Livewire\Component;
use App\Events\Admin\Selfs\Create as CreateEvent;
use App\Events\Admin\Selfs\Edit as EditEvent;

class SelfCheckoutForm extends Component
{
    public $selfId;
    public $sel_name;
    public $sel_pdv_ip;
    public $sel_rtsp_url;
    public $sel_uni_id;
    public $sel_status = false;
    
    public $isEdit = false;
    public $unidades = [];
    
    protected $listeners = [
        'load' => '$refresh',
    ];
    
    protected function rules()
    {
        $uniqueRuleIp = 'required|ip|unique:selfs,sel_pdv_ip';
        $uniqueRuleRtsp = 'required|url|unique:selfs,sel_rtsp_url';
        
        if ($this->isEdit) {
            $uniqueRuleIp .= ',' . $this->selfId . ',sel_id';
            $uniqueRuleRtsp .= ',' . $this->selfId . ',sel_id';
        }
        
        return [
            'sel_name' => 'required|string|max:255',
            'sel_pdv_ip' => $uniqueRuleIp,
            'sel_rtsp_url' => $uniqueRuleRtsp,
            'sel_uni_id' => 'required|exists:unidade,uni_id',
            'sel_status' => 'boolean'
        ];
    }
    
    protected $messages = [
        'sel_name.required' => 'O nome do SelfCheckout é obrigatório.',
        'sel_name.string' => 'O nome do SelfCheckout deve ser um texto válido.',
        'sel_name.max' => 'O nome do SelfCheckout não pode ter mais de 255 caracteres.',
        
        'sel_pdv_ip.required' => 'O endereço IP é obrigatório.',
        'sel_pdv_ip.ip' => 'O endereço IP informado não é válido.',
        'sel_pdv_ip.unique' => 'Este endereço IP já está em uso por outro SelfCheckout.',
        
        'sel_rtsp_url.required' => 'A URL RTSP é obrigatória.',
        'sel_rtsp_url.url' => 'A URL RTSP informada não é válida.',
        'sel_rtsp_url.unique' => 'Esta URL RTSP já está em uso por outro SelfCheckout.',
        
        'sel_uni_id.required' => 'A unidade é obrigatória.',
        'sel_uni_id.exists' => 'A unidade selecionada não é válida.'
    ];
    
    public function mount($self = null)
    {
        $this->unidades = Unidade::all();
        
        if ($self) {
            if (is_numeric($self) || is_string($self)) {
                $self = Selfs::findOrFail($self);
            }
            
            $this->selfId = $self->sel_id;
            $this->sel_name = $self->sel_name;
            $this->sel_pdv_ip = $self->sel_pdv_ip;
            $this->sel_rtsp_url = $self->sel_rtsp_url;
            $this->sel_uni_id = $self->sel_uni_id;
            $this->sel_status = (bool) $self->sel_status;
            $this->isEdit = true;
        }
    }
    
    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
    }
    
    public function save()
    {
        $validatedData = $this->validate();
        $validatedData['sel_status'] = $this->sel_status ? 1 : 0;
        
        try {
            if ($this->isEdit) {
                $self = Selfs::findOrFail($this->selfId);
                $self->update($validatedData);
                
                event(new EditEvent($self->sel_id, request()->ip()));
                
                session()->flash('success', 'SelfCheckout atualizado com sucesso.');
            } else {
                $self = Selfs::create($validatedData);
                
                event(new CreateEvent($self->sel_id, request()->ip()));
                
                session()->flash('success', 'SelfCheckout criado com sucesso.');
            }
            
            return redirect()->route('selfs.index');
        } catch (\Exception $e) {
            session()->flash('error', 'Ocorreu um erro: ' . $e->getMessage());
        }
    }
    
    public function render()
    {
        $title = $this->isEdit ? 'Editar SelfCheckout' : 'Criar Novo SelfCheckout';
        
        return view('livewire.admin.selfs.form', [
            'title' => $title
        ])->extends('adminlte::page')
          ->section('content');
    }
}
