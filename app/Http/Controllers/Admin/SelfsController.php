<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Selfs;
use Illuminate\Http\Request;

class SelfsController extends Controller
{
    public function toggleStatus(Selfs $self)
    {
        try {
            $self->sel_status = !$self->sel_status;
            $self->save();
            
            event(new \App\Events\Admin\Selfs\Edit($self->sel_id, request()->ip()));
            
            $statusText = $self->sel_status ? 'ativado' : 'desativado';
            
            return response()->json([
                'status' => 'success',
                'message' => "SelfCheckout {$statusText} com sucesso."
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Erro ao alterar o status do SelfCheckout.'
            ], 500);
        }
    }
}