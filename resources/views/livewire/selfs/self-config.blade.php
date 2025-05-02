<div>
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-tv mr-2"></i>Configurações de Tela
            </h3>
        </div>
        
        <div class="card-body">
            @if(session()->has('error'))
                <div class="alert alert-danger">
                    {{ session('error') }}
                </div>
            @endif

            @if(session()->has('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif

            <div class="row">
                <div class="col-md-3">
                    <div class="form-group">
                        <label>Quantidade de Telas</label>
                        <select wire:model="selectedQuadrants" class="form-control">
                            <option value="0">Selecione a quantidade de telas</option>
                            @foreach($quadrantOptions as $option)
                                <option value="{{ $option }}">{{ $option }} Telas</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                
                <div class="col-md-3">
                    <div class="form-group">
                        <label>Número de Colunas</label>
                        <select 
                            wire:model="selectedColumns" 
                            class="form-control" 
                            {{ $selectedQuadrants == 0 ? 'disabled' : '' }}
                        >
                            <option value="0">Selecione o número de colunas</option>
                            @if($selectedQuadrants > 0)
                                @for($i = 1; $i <= $selectedQuadrants; $i++)
                                    @if($selectedQuadrants % $i == 0)
                                        <option value="{{ $i }}">{{ $i }} Coluna(s)</option>
                                    @endif
                                @endfor
                            @endif
                        </select>
                    </div>
                </div>
                
                {{-- <div class="col-md-3">
                    <div class="form-group">
                        <label>Número de Linhas</label>
                        <input 
                            type="text" 
                            class="form-control" 
                            value="{{ $selectedColumns > 0 ? ($selectedQuadrants / $selectedColumns) : '' }}" 
                            readonly
                        >
                    </div>
                </div> --}}
            </div>

            @if($layoutPreviewHtml)
                <div class="row mt-3">
                    <div class="col-12">
                        {!! $layoutPreviewHtml !!}
                    </div>
                </div>
            @endif

            @if($selectedQuadrants > 0 && $selectedColumns > 0)
                <div class="row mt-3">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">Mapeamento de PDVs</div>
                            <div class="card-body">
                                <div class="screen-grid" style="
                                    display: grid;
                                    grid-template-columns: repeat({{ $selectedColumns }}, 1fr);
                                    gap: 15px;
                                    padding: 15px;
                                ">
                                    @for($i = 1; $i <= $selectedQuadrants; $i++)
                                        <div class="screen-item">
                                            <label>Tela {{ $i }}</label>
                                            <select 
                                                class="form-control" 
                                                wire:model.lazy="selectedPdvs.{{ $i }}"
                                            >
                                                <option value="">Selecione um PDV</option>
                                                @foreach($pdvs as $pdv)
                                                    <option value="{{ $pdv['id'] }}">
                                                        {{ $pdv['nome'] }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    @endfor
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <div class="card-footer">
                <button 
                    wire:click="applyConfiguration" 
                    class="btn btn-primary"
                    {{ !$this->isConfigurationValid() ? 'disabled' : '' }}
                >
                    <i class="fas fa-check mr-2"></i>Criar preferência
                    @if(!$this->isConfigurationValid())
                        <small class="text-warning">(Configuração inválida)</small>
                    @endif
                </button>
            </div>
        </div>
    </div>
</div>