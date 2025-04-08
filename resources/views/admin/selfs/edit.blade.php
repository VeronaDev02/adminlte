@extends('adminlte::page')

@section('title', 'Editar SelfCheckout')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1 class="mx-auto" style="font-weight: bold;">Editar Selfcheckout</h1>
    </div>
    <div class="d-flex justify-content-between align-items-center">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0 bg-transparent p-0">
                <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('selfs.index') }}">SelfCheckouts</a></li>
                <li class="breadcrumb-item active" aria-current="page">Editar SelfCheckouts</li>
            </ol>
        </nav>
    </div>
@stop

@section('content')
    <div class="card">
        <div class="card-body">
            <form action="{{ route('selfs.update', $self->sel_id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="sel_name">Nome do SelfCheckout</label>
                            <input type="text" class="form-control @error('sel_name') is-invalid @enderror" 
                                   id="sel_name" name="sel_name" value="{{ old('sel_name', $self->sel_name) }}" 
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
                                   id="sel_pdv_ip" name="sel_pdv_ip" value="{{ old('sel_pdv_ip', $self->sel_pdv_ip) }}" 
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
                            <label for="sel_rtsp_url">URL RTSP (opcional)</label>
                            <input type="text" class="form-control @error('sel_rtsp_url') is-invalid @enderror" 
                                   id="sel_rtsp_url" name="sel_rtsp_url" value="{{ old('sel_rtsp_url', $self->sel_rtsp_url) }}" 
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
                                        {{ old('sel_uni_id', $self->sel_uni_id) == $unidade->uni_id ? 'selected' : '' }}>
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
                                       value="1" {{ old('sel_status', $self->sel_status) ? 'checked' : '' }}>
                                <label class="custom-control-label" for="sel_status">
                                    {{ old('sel_status', $self->sel_status) ? 'Ativo' : 'Inativo' }}
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row mt-3">
                    <div class="col-12">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Atualizar
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
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const statusSwitch = document.getElementById('sel_status');
        const statusLabel = statusSwitch.nextElementSibling;

        statusSwitch.addEventListener('change', function() {
            statusLabel.textContent = this.checked ? 'Ativo' : 'Inativo';
        });
    });
</script>
@stop