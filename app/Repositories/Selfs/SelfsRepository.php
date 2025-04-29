<?php

namespace App\Repositories\Selfs;

use App\Models\Selfs;
use App\Models\Unidade;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Collection;

class SelfsRepository
{
    public function getUserSelfs(): Collection
    {
        $user = Auth::user();
        
        return $user->unidades()
            ->with('selfs')
            ->get()
            ->flatMap(function($unidade) {
                return $unidade->selfs()->active()->get();
            });
    }

    public function preparePdvDataList(Collection $selfsList): array
    {
        return $selfsList->map(function ($self) {
            return [
                'id' => $self->sel_id,
                'nome' => $self->sel_name,
                'pdvIp' => $self->sel_pdv_ip,
                'pdvCodigo' => $self->sel_pdv_codigo,
                'rtspUrl' => $self->sel_rtsp_path,
            ];
        })->toArray();
    }

    public function orderSelectedPdvs(array $pdvDataList, array $selectedPdvs): array
    {
        if (empty($selectedPdvs)) {
            return $pdvDataList;
        }

        $orderedPdvList = collect($pdvDataList)
            ->sortBy(function($pdv) use ($selectedPdvs) {
                return !in_array($pdv['id'], $selectedPdvs);
            })
            ->values()
            ->all();

        return $orderedPdvList;
    }

    public function findPdvById(array $pdvDataList, $pdvId)
    {
        return collect($pdvDataList)->firstWhere('id', $pdvId);
    }
}