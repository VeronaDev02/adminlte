@extends('adminlte::page')
@component('components.alert.sweet-alert')

@endcomponent
@section('title', '- Criar novo SelfCheckout')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1 class="mx-auto" style="font-weight: bold;">Criar Novo SelfCheckout</h1>
    </div>
    <div class="d-flex justify-content-between align-items-center">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0 bg-transparent p-0">
                <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('selfs.index') }}">SelfCheckouts</a></li>
                <li class="breadcrumb-item active" aria-current="page">Criar Novo SelfCheckout</li>
            </ol>
        </nav>
    </div>
@stop

@section('content')
    <div class="card">
        <div class="card-body">
            <form id="create-form" action="{{ route('selfs.store') }}" method="POST">
                @csrf
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="sel_name">Nome do SelfCheckout</label>
                            <input type="text" class="form-control @error('sel_name') is-invalid @enderror" 
                                   id="sel_name" name="sel_name" value="{{ old('sel_name') }}" 
                                   placeholder="Digite o nome do SelfCheckout" required>
                            @error('sel_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="sel_pdv_ip">Endereço IP</label>
                            <input type="text" class="form-control @error('sel_pdv_ip') is-invalid @enderror" 
                                   id="sel_pdv_ip" name="sel_pdv_ip" value="{{ old('sel_pdv_ip') }}" 
                                   placeholder="Digite o endereço IP" required>
                            @error('sel_pdv_ip')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="sel_rtsp_url">URL RTSP</label>
                            <input type="text" class="form-control @error('sel_rtsp_url') is-invalid @enderror" 
                                   id="sel_rtsp_url" name="sel_rtsp_url" value="{{ old('sel_rtsp_url') }}" 
                                   placeholder="Digite a URL RTSP">
                            @error('sel_rtsp_url')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="sel_uni_id">Unidade</label>
                            <select class="form-control @error('sel_uni_id') is-invalid @enderror" 
                                    id="sel_uni_id" name="sel_uni_id" required>
                                <option value="">Selecione uma Unidade</option>
                                @foreach($unidades as $unidade)
                                    <option value="{{ $unidade->uni_id }}" 
                                        {{ old('sel_uni_id') == $unidade->uni_id ? 'selected' : '' }}>
                                        {{ $unidade->uni_codigo }} - {{ $unidade->uni_descricao }}
                                    </option>
                                @endforeach
                            </select>
                            @error('sel_uni_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="sel_status">Status</label>
                            <div class="custom-control custom-switch">
                                <input type="checkbox" class="custom-control-input" 
                                       id="sel_status" name="sel_status" 
                                       value="0" {{ old('sel_status') ? 'checked' : '' }}>
                                <label class="custom-control-label" for="sel_status">
                                    {{ old('sel_status') ? 'Ativo' : 'Inativo' }}
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row mt-3">
                    <div class="col-12">
                        <button type="submit" class="btn btn-primary" id="save-button">
                            <i class="fas fa-save"></i> Salvar
                        </button>
                        <a href="{{ route('selfs.index') }}" class="btn btn-secondary ml-2">
                            <i class="fas fa-arrow-left"></i> Voltar
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>
@stop

@section('js')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        initializeForm();
        setupStatusSwitch();
        setupIpMask();
    });

    function initializeForm() {
        @if($errors->any())
            mostrarErro('Por favor, verifique os campos do formulário.', 'Erro de Validação');
        @endif

        const form = document.getElementById('create-form');
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const statusSwitch = document.getElementById('sel_status');
            statusSwitch.value = statusSwitch.checked ? 1 : 0;
            
            const formData = new FormData(form);
            const saveButton = document.getElementById('save-button');
            const originalText = saveButton.innerHTML;
            
            updateButtonState(saveButton, true, originalText);
            
            sendFormData(form, formData, saveButton, originalText);
        });
    }

    function updateButtonState(button, isLoading, originalText) {
        if (isLoading) {
            button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Salvando...';
            button.disabled = true;
        } else {
            button.innerHTML = originalText;
            button.disabled = false;
        }
    }

    function sendFormData(form, formData, saveButton, originalText) {
        fetch(form.action, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(Object.fromEntries(formData))
        })
        .then(response => {
            // Verificar se a resposta é JSON
            const contentType = response.headers.get('content-type');
            if (!contentType || !contentType.includes('application/json')) {
                throw new Error('Resposta não é JSON');
            }
            return response.json();
        })
        .then(data => handleFormResponse(data, saveButton, originalText))
        .catch(error => {
            console.error('Erro completo:', error);
            handleFormError(error, saveButton, originalText);
        });
    }

    function handleFormResponse(data, saveButton, originalText) {
        if (data.success) {
            localStorage.setItem('redirectMessage', data.message || 'SelfCheckout criado com sucesso.');
            localStorage.setItem('redirectMessageType', 'success');
            
            window.location.href = "{{ route('selfs.index') }}";
        } else {
            updateButtonState(saveButton, false, originalText);
            
            if (data.errors) {
                const errorMessages = Object.values(data.errors).flat();
                mostrarErro(errorMessages.join('<br>'), 'Erro de Validação');
            } else {
                mostrarErro(data.message || 'Ocorreu um erro ao criar o SelfCheckout.', 'Erro');
            }
        }
    }

    function handleFormError(error, saveButton, originalText) {
        updateButtonState(saveButton, false, originalText);
        
        // Log detalhado do erro
        console.error('Erro de envio:', error);
        
        // Mensagem amigável
        mostrarErro('Não foi possível processar a solicitação. Verifique sua conexão e tente novamente.', 'Erro de Comunicação');
    }

    function setupStatusSwitch() {
        const statusSwitch = document.getElementById('sel_status');
        const statusLabel = statusSwitch.nextElementSibling;

        statusSwitch.addEventListener('change', function() {
            statusLabel.textContent = this.checked ? 'Ativo' : 'Inativo';
        });
    }

    function setupIpMask() {
        $(document).ready(function(){
            $('#sel_pdv_ip').mask('0ZZ.0ZZ.0ZZ.0ZZ', {
                translation: {
                    'Z': {pattern: /[0-9]/, optional: true}
                }
            });
        });
    }
</script>
@stop