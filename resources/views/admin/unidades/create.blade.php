@extends('adminlte::page')

@section('title', 'Nova Unidade')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1 class="mx-auto" style="font-weight: bold;">Nova Unidade</h1>
    </div>
    <div class="d-flex justify-content-between align-items-center">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0 bg-transparent p-0">
                <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('unidades.index') }}">Unidades</a></li>
                <li class="breadcrumb-item active" aria-current="page">Nova Unidade</li>
            </ol>
        </nav>
    </div>
@stop

@section('content')
    <div class="card">
        <div class="card-body">
            <form action="{{ route('unidades.store') }}" method="POST">
                @csrf
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="uni_descricao">Nome</label>
                            <input type="text" class="form-control @error('uni_descricao') is-invalid @enderror" id="uni_descricao" name="uni_descricao" value="{{ old('uni_descricao') }}" required>
                            @error('uni_descricao')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="uni_codigo">Código</label>
                            <input type="text" class="form-control @error('uni_codigo') is-invalid @enderror" id="uni_codigo" name="uni_codigo" value="{{ old('uni_codigo') }}" required>
                            @error('uni_codigo')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="uni_cidade">Cidade</label>
                            <input type="text" class="form-control @error('uni_cidade') is-invalid @enderror" id="uni_cidade" name="uni_cidade" value="{{ old('uni_cidade') }}" required>
                            @error('uni_cidade')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="uni_uf">UF</label>
                            <select class="form-control @error('uni_uf') is-invalid @enderror" id="uni_uf" name="uni_uf" required>
                                <option value="">Selecione...</option>
                                <option value="AC" {{ old('uni_uf') == 'AC' ? 'selected' : '' }}>AC</option>
                                <option value="AL" {{ old('uni_uf') == 'AL' ? 'selected' : '' }}>AL</option>
                                <option value="AM" {{ old('uni_uf') == 'AM' ? 'selected' : '' }}>AM</option>
                                <option value="AP" {{ old('uni_uf') == 'AP' ? 'selected' : '' }}>AP</option>
                                <option value="BA" {{ old('uni_uf') == 'BA' ? 'selected' : '' }}>BA</option>
                                <option value="CE" {{ old('uni_uf') == 'CE' ? 'selected' : '' }}>CE</option>
                                <option value="DF" {{ old('uni_uf') == 'DF' ? 'selected' : '' }}>DF</option>
                                <option value="ES" {{ old('uni_uf') == 'ES' ? 'selected' : '' }}>ES</option>
                                <option value="GO" {{ old('uni_uf') == 'GO' ? 'selected' : '' }}>GO</option>
                                <option value="MA" {{ old('uni_uf') == 'MA' ? 'selected' : '' }}>MA</option>
                                <option value="MG" {{ old('uni_uf') == 'MG' ? 'selected' : '' }}>MG</option>
                                <option value="MS" {{ old('uni_uf') == 'MS' ? 'selected' : '' }}>MS</option>
                                <option value="MT" {{ old('uni_uf') == 'MT' ? 'selected' : '' }}>MT</option>
                                <option value="PA" {{ old('uni_uf') == 'PA' ? 'selected' : '' }}>PA</option>
                                <option value="PB" {{ old('uni_uf') == 'PB' ? 'selected' : '' }}>PB</option>
                                <option value="PE" {{ old('uni_uf') == 'PE' ? 'selected' : '' }}>PE</option>
                                <option value="PI" {{ old('uni_uf') == 'PI' ? 'selected' : '' }}>PI</option>
                                <option value="PR" {{ old('uni_uf') == 'PR' ? 'selected' : '' }}>PR</option>
                                <option value="RJ" {{ old('uni_uf') == 'RJ' ? 'selected' : '' }}>RJ</option>
                                <option value="RN" {{ old('uni_uf') == 'RN' ? 'selected' : '' }}>RN</option>
                                <option value="RO" {{ old('uni_uf') == 'RO' ? 'selected' : '' }}>RO</option>
                                <option value="RR" {{ old('uni_uf') == 'RR' ? 'selected' : '' }}>RR</option>
                                <option value="RS" {{ old('uni_uf') == 'RS' ? 'selected' : '' }}>RS</option>
                                <option value="SC" {{ old('uni_uf') == 'SC' ? 'selected' : '' }}>SC</option>
                                <option value="SE" {{ old('uni_uf') == 'SE' ? 'selected' : '' }}>SE</option>
                                <option value="SP" {{ old('uni_uf') == 'SP' ? 'selected' : '' }}>SP</option>
                                <option value="TO" {{ old('uni_uf') == 'TO' ? 'selected' : '' }}>TO</option>
                            </select>
                            @error('uni_uf')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
                
                <div class="d-flex justify-content-end mt-3 mb-4">
                    <a href="{{ route('unidades.index') }}" class="btn btn-secondary mr-2">
                        <i class="fas fa-times"></i> Cancelar
                    </a>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-save"></i> Salvar
                    </button>
                </div>
            </form>
        </div>
    </div>
@stop

@section('css')
    <style>
        .card-outline {
            border-top: 3px solid;
        }
        .card-outline.card-primary {
            border-top-color: #007bff;
        }
    </style>
@stop

@section('js')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const codigoInput = document.getElementById('uni_codigo');
        
        if(codigoInput) {
            codigoInput.addEventListener('input', function(e) {
                // Remove qualquer caractere que não seja número
                let value = e.target.value.replace(/\D/g, '');
                
                // Limite de 3 dígitos, geralmente a unidade é 00 + dígitos
                if (value.length > 3) {
                    value = value.substring(0, 3);
                }
                
                // Atualiza o valor do campo
                e.target.value = value;
            });
        }
    });
</script>
@stop