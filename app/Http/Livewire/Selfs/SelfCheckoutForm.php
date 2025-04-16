<?php

namespace App\Http\Livewire\Selfs;

use App\Models\Selfs;
use App\Models\Unidade;
use Livewire\Component;
use Illuminate\Support\Facades\Crypt;
use App\Events\Admin\Selfs\Create as CreateEvent;
use App\Events\Admin\Selfs\Edit as EditEvent;

class SelfCheckoutForm extends Component
{
    public $selfId;
    public $sel_name;
    public $sel_pdv_ip;
    public $sel_dvr_ip;
    public $sel_dvr_username;
    public $sel_dvr_password;
    public $sel_camera_canal;
    public $sel_dvr_porta;
    public $sel_rtsp_path;
    public $sel_uni_id;
    public $sel_status = false;
    
    public $isEdit = false;
    public $unidades = [];
    
    protected $listeners = [
        'load' => '$refresh',
        'set:sel_rtsp_path' => 'setRtspPath'
    ];
    
    public function setRtspPath($value)
    {
        $this->sel_rtsp_path = $value;
    }
    
    protected function rules()
    {
        return [
            'sel_name' => 'required|string|max:255',
            'sel_pdv_ip' => 'required|ipv4',
            'sel_dvr_ip' => 'required|ipv4',
            'sel_dvr_username' => 'required|string|max:255',
            'sel_dvr_password' => 'required|string|max:255',
            'sel_camera_canal' => 'required|string|max:255',
            'sel_dvr_porta' => 'required|numeric|max:65535',
            'sel_rtsp_path' => 'required|string|max:250',
            'sel_uni_id' => 'required|exists:unidade,uni_id',
            'sel_status' => 'boolean'
        ];
    }
    
    protected $messages = [
        'sel_name.required' => 'O nome do SelfCheckout é obrigatório.',
        'sel_name.string' => 'O nome do SelfCheckout deve ser um texto válido.',
        'sel_name.max' => 'O nome do SelfCheckout não pode ter mais de 255 caracteres.',
        
        'sel_pdv_ip.required' => 'O endereço IP do PDV é obrigatório.',
        'sel_pdv_ip.ipv4' => 'O endereço IP do PDV informado não é válido.',
        
        'sel_dvr_ip.required' => 'O endereço IP do DVR é obrigatório.',
        'sel_dvr_ip.ipv4' => 'O endereço IP do DVR informado não é válido.',
        
        'sel_dvr_username.required' => 'O nome de usuário do DVR é obrigatório.',
        'sel_dvr_password.required' => 'A senha do DVR é obrigatória.',
        'sel_camera_canal.required' => 'O canal da câmera é obrigatório.',
        'sel_dvr_porta.required' => 'A porta do DVR é obrigatória.',
        'sel_rtsp_path.required' => 'O caminho RTSP é obrigatório.',
        
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
            $this->sel_dvr_ip = $self->sel_dvr_ip;
            $this->sel_dvr_username = $self->sel_dvr_username;
            $this->sel_dvr_password = $self->sel_dvr_password;
            $this->sel_camera_canal = $self->sel_camera_canal;
            $this->sel_dvr_porta = $self->sel_dvr_porta;
            $this->sel_rtsp_path = $self->sel_rtsp_path;
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
        try {
            $validatedData = $this->validate();
            
            $validatedData['sel_status'] = $this->sel_status ? 1 : 0;
            
            if ($this->isEdit) {
                $self = Selfs::findOrFail($this->selfId);
                $self->update($validatedData);
                
                event(new EditEvent($self->sel_id, request()->ip()));
                
                session()->flash('success', 'SelfCheckout atualizado com sucesso.');
            } else {
                $self = Selfs::create($validatedData);
                
                if (!$self || !$self->sel_id) {
                    throw new \Exception('Falha ao criar SelfCheckout - ID não gerado');
                }
                
                event(new CreateEvent($self->sel_id, request()->ip()));
                
                session()->flash('success', 'SelfCheckout criado com sucesso.');
            }
            
            return redirect()->route('selfs.index');
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Dispara evento de erros de validação para o frontend
            $this->dispatchBrowserEvent('validation-errors', [
                'errors' => $e->validator->errors()->all()
            ]);
            return back()->withErrors($e->validator);
        } catch (\Exception $e) {
            session()->flash('error', 'Erro ao salvar SelfCheckout: ' . $e->getMessage());
            return back();
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