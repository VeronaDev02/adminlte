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
    public $sel_pdv_codigo;
    
    public $isEdit = false;
    public $unidades = [];
    
    protected $listeners = [
        'load' => '$refresh',
        'set:sel_rtsp_path' => 'setRtspPath',
        'select2:updated' => 'handleSelect2Updated'
    ];
    
    public function handleSelect2Updated($name, $value)
    {
        if ($name === 'sel_uni_id') {
            $this->sel_uni_id = $value;
        }
    }

    protected function getListeners()
    {
        return array_merge($this->listeners, [
            'select2:updated' => 'handleSelect2Updated'
        ]);
    }

    public function getSelRtspPathAttribute($value)
    {
        try {
            if (empty($value)) {
                return null;
            }
            return Crypt::decryptString($value);
        } catch (\Exception $e) {
            \Log::error('Erro ao descriptografar sel_rtsp_path', [
                'error' => $e->getMessage(),
                'value_length' => strlen($value ?? '')
            ]);
            return null;
        }
    }

    public function validateRtspPath()
    {
        if (strlen($this->sel_rtsp_path) > 250) {
            $this->sel_rtsp_path = substr($this->sel_rtsp_path, 0, 250);
            $this->dispatchBrowserEvent('toastr:warning', [
                'message' => 'O caminho RTSP foi truncado para 250 caracteres.'
            ]);
        }
        return true;
    }

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
            'sel_status' => 'boolean',
            'sel_pdv_codigo' => 'required|string|max:3',
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
        'sel_uni_id.exists' => 'A unidade selecionada não é válida.',

        'sel_pdv_codigo.required' => 'O código do PDV é obrigatório',
        'sel_pdv_codigo.max' => 'O código do PDV não pode ter mais de 3 caracteres.',
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
            $this->sel_pdv_codigo = $self->sel_pdv_codigo;
            $this->isEdit = true;
        }
    }
    
    public function updated($field)
    {
        if ($field === 'sel_uni_id') {
            $unidade = Unidade::find($this->sel_uni_id);
            if (!$unidade) {
                $this->sel_uni_id = null;
            }
        }
        
        $this->validateOnly($field);
        
        if ($field === 'sel_rtsp_path') {
            $this->validateRtspPath();
        }
    }
    
    public function save()
    {
        try {
            // Adicionar log de depuração
            \Log::info('Iniciando salvamento de SelfCheckout', [
                'dados' => $this->only(['sel_name', 'sel_pdv_ip', 'sel_uni_id', 'sel_status']),
                'isEdit' => $this->isEdit
            ]);
            
            $validatedData = $this->validate();
            
            $validatedData['sel_status'] = $this->sel_status ? 1 : 0;

            // Log após validação
            \Log::info('Dados validados com sucesso');

            if ($this->isEdit) {
                $self = Selfs::findOrFail($this->selfId);
                $self->update($validatedData);
                
                event(new EditEvent($self->sel_id, request()->ip()));
                
                $this->dispatchBrowserEvent('toastr:success', [
                    'message' => 'SelfCheckout atualizado com sucesso.'
                ]);
                
                // Log sucesso na edição
                \Log::info('SelfCheckout atualizado', ['id' => $self->sel_id]);
                
                session()->flash('success', 'SelfCheckout atualizado com sucesso.');
            } else {
                $self = Selfs::create($validatedData);
                
                if (!$self || !$self->sel_id) {
                    // Log erro específico
                    \Log::error('Falha ao criar SelfCheckout - ID não gerado');
                    throw new \Exception('Falha ao criar SelfCheckout - ID não gerado');
                }
                
                event(new CreateEvent($self->sel_id, request()->ip()));
                
                $this->dispatchBrowserEvent('toastr:success', [
                    'message' => 'SelfCheckout criado com sucesso.'
                ]);
                
                // Log sucesso na criação
                \Log::info('SelfCheckout criado', ['id' => $self->sel_id]);
                
                session()->flash('success', 'SelfCheckout criado com sucesso.');
            }
            
            return redirect()->route('selfs.index');
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Log erro de validação
            \Log::error('Erro de validação', [
                'errors' => $e->validator->errors()->all()
            ]);
            
            $this->dispatchBrowserEvent('validation-errors', [
                'errors' => $e->validator->errors()->all()
            ]);
            
            $this->dispatchBrowserEvent('toastr:error', [
                'message' => 'Verifique os campos e tente novamente.'
            ]);
            
            return null;
        } catch (\Exception $e) {
            // Log erro genérico
            \Log::error('Erro ao salvar SelfCheckout', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            $this->dispatchBrowserEvent('toastr:error', [
                'message' => 'Erro ao salvar SelfCheckout: ' . $e->getMessage()
            ]);
            
            session()->flash('error', 'Erro ao salvar SelfCheckout: ' . $e->getMessage());
            return null;
        }
    }
    
    public function render()
    {
        $title = $this->isEdit ? 'Editar SelfCheckout' : 'Criar Novo SelfCheckout';
        
        $this->dispatchBrowserEvent('contentChanged');

        return view('livewire.admin.selfs.form', [
            'title' => $title
        ])->extends('adminlte::page')
          ->section('content');
    }
}