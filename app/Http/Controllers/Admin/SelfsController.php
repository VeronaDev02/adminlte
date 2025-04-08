<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Selfs;
use App\Models\Unidade;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SelfsController extends Controller
{
    /**
     * Exibe a lista de PDVs
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $selfs = Selfs::with('unidade')->paginate(10);
        return view('admin.selfs.index', compact('selfs'));
    }

    /**
     * Mostra o formulário de criação de um novo PDV
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        $unidades = Unidade::all();
        return view('admin.selfs.create', compact('unidades'));
    }

    /**
     * Salva um novo PDV
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        // Validação dos dados
        $validator = Validator::make($request->all(), [
            'sel_name' => 'required|string|max:255',
            'sel_pdv_ip' => 'required|ip',
            'sel_rtsp_url' => 'nullable|url',
            'sel_uni_id' => 'required|exists:unidade,uni_id',
            'sel_status' => 'boolean'
        ]);

        // Verifica se a validação falhou
        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Cria o novo PDV
        Selfs::create($validator->validated());

        // Redireciona com mensagem de sucesso
        return redirect()->route('selfs.index')
            ->with('success', 'PDV criado com sucesso.');
    }

    /**
     * Mostra o formulário de edição de um PDV
     *
     * @param  \App\Models\Selfs  $self
     * @return \Illuminate\View\View
     */
    public function edit(Selfs $self)
    {
        $unidades = Unidade::all();
        return view('admin.selfs.edit', compact('self', 'unidades'));
    }

    /**
     * Atualiza um PDV existente
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Selfs  $self
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Selfs $self)
    {
        // Validação dos dados
        $validator = Validator::make($request->all(), [
            'sel_name' => 'required|string|max:255',
            'sel_pdv_ip' => 'required|ip',
            'sel_rtsp_url' => 'nullable|url',
            'sel_uni_id' => 'required|exists:unidade,uni_id',
            'sel_status' => 'boolean'
        ]);

        // Verifica se a validação falhou
        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Atualiza o PDV
        $self->update($validator->validated());

        // Redireciona com mensagem de sucesso
        return redirect()->route('selfs.index')
            ->with('success', 'PDV atualizado com sucesso.');
    }

    /**
     * Exclui um PDV
     *
     * @param  \App\Models\Selfs  $self
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Selfs $self)
    {
        try {
            $self->delete();
            return redirect()->route('selfs.index')
                ->with('success', 'PDV excluído com sucesso.');
        } catch (\Exception $e) {
            return redirect()->route('selfs.index')
                ->with('error', 'Não foi possível excluir o PDV.');
        }
    }

    /**
     * Altera o status de um PDV
     *
     * @param  \App\Models\Selfs  $self
     * @return \Illuminate\Http\RedirectResponse
     */
    public function toggleStatus(Selfs $self)
    {
        $self->sel_status = !$self->sel_status;
        $self->save();

        return response()->json([
            'status' => $self->sel_status
        ]);
    }
}