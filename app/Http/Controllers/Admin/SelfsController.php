<?php

namespace App\Http\Controllers\Admin;

use App\Events\Admin\Selfs\Create as CreateEvent;
use App\Events\Admin\Selfs\Edit as EditEvent;
use App\Events\Admin\Selfs\Delete as DeleteEvent;
use App\Http\Controllers\Controller;
use App\Models\Selfs;
use App\Models\Unidade;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SelfsController extends Controller
{
    public function index()
    {
        $selfs = Selfs::with('unidade')->paginate(10);
        return view('admin.selfs.index', compact('selfs'));
    }

    public function create()
    {
        $unidades = Unidade::all();
        return view('admin.selfs.create', compact('unidades'));
    }

    public function store(Request $request)
    {
        try {
            // Validação dos dados com mensagens personalizadas
            $validator = Validator::make($request->all(), [
                'sel_name' => 'required|string|max:255',
                'sel_pdv_ip' => 'required|ip|unique:selfs,sel_pdv_ip',
                'sel_rtsp_url' => 'required|url|unique:selfs,sel_rtsp_url',
                'sel_uni_id' => 'required|exists:unidade,uni_id',
                'sel_status' => 'boolean'
            ], [
                // Mensagens de erro personalizadas
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
                'sel_uni_id.exists' => 'A unidade selecionada não é válida.',
                
                'sel_status.boolean' => 'O status deve ser um valor booleano válido.'
            ]);

            // Verifica se a validação falhou
            if ($validator->fails()) {
                // Resposta JSON específica para erros de validação
                if ($request->expectsJson() || $request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'errors' => $validator->errors()->all()
                    ], 422);
                }

                return redirect()->back()
                    ->withErrors($validator)
                    ->withInput();
            }

            $validatedData = $validator->validated();
            $validatedData['sel_status'] = $request->has('sel_status') ? 1 : 0;
            $self = Selfs::create($validatedData);
            
            event(new CreateEvent($self->sel_id, request()->ip()));

            // Garantir resposta JSON para requisições AJAX
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'SelfCheckout criado com sucesso.',
                    'redirect' => route('selfs.index')
                ]);
            }

            // Redireciona com mensagem de sucesso para requisições normais
            return redirect()->route('selfs.index')
                ->with('success', 'SelfCheckout criado com sucesso.');

        } catch (\Exception $e) {
            // Tratamento de erro genérico
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Erro ao criar SelfCheckout: ' . $e->getMessage()
                ], 500);
            }

            // Para requisições não-AJAX
            return redirect()->back()
                ->with('error', 'Erro ao criar SelfCheckout: ' . $e->getMessage());
        }
    }

    public function edit(Selfs $self)
    {
        $unidades = Unidade::all();
        return view('admin.selfs.edit', compact('self', 'unidades'));
    }

    public function update(Request $request, Selfs $self)
    {
        // Validação dos dados
        $validator = Validator::make($request->all(), [
            'sel_name' => 'required|string|max:255',
            'sel_pdv_ip' => 'required|ip|unique:selfs,sel_pdv_ip,'.$self->sel_id.',sel_id',
            'sel_rtsp_url' => 'required|url|unique:selfs,sel_rtsp_url,'.$self->sel_id.',sel_id',
            'sel_uni_id' => 'required|exists:unidade,uni_id',
            'sel_status' => 'boolean'
        ],[
            // Mensagens de erro personalizadas
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
            'sel_uni_id.exists' => 'A unidade selecionada não é válida.',
            
            'sel_status.boolean' => 'O status deve ser um valor booleano válido.'
        ]);

        // Verifica se a validação falhou
        if ($validator->fails()) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], 422);
            }
            
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Pegar os dados que foram validados
        $validatedData = $validator->validated();

        // Define o status
        $validatedData['sel_status'] = $request->has('sel_status') ? 1 : 0;

        // Atualiza o SelfCheckout
        $self->update($validatedData);

        event(new EditEvent($self->sel_id, request()->ip()));
        
        // Retorna resposta JSON em vez de redirecionar
        return response()->json([
            'success' => true,
            'message' => 'SelfCheckout atualizado com sucesso.',
            'data' => $self
        ]);
    }

    public function destroy(Selfs $self)
    {
        event(new DeleteEvent(("NOME SelfCheckout: " . $self->sel_name . " ID - Unidade: " . $self->sel_uni_id), request()->ip()));
        try {
            $self->delete();
            
            if (request()->expectsJson() || request()->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'SelfCheckout excluído com sucesso.',
                    'redirect' => route('selfs.index')
                ]);
            }
            
            return redirect()->route('selfs.index')
                ->with('success', 'SelfCheckout excluído com sucesso.');
        } catch (\Exception $e) {
            if (request()->expectsJson() || request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Não foi possível excluir o SelfCheckout.'
                ], 500);
            }
            
            return redirect()->route('selfs.index')
                ->with('error', 'Não foi possível excluir o SelfCheckout.');
        }
    }

    public function toggleStatus(Selfs $self)
    {
        $self->sel_status = !$self->sel_status;
        $self->save();

        event(new EditEvent($self->sel_id, request()->ip()));

        return response()->json([
            'status' => $self->sel_status
        ]);
    }
}