<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Unidade;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class UnidadeController extends Controller
{
    /**
     * Exibe uma lista de todas as unidades.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $unidades = Unidade::orderBy('uni_id', 'asc')->paginate(15);
        return view('admin.unidades.index', compact('unidades'));
    }

    /**
     * Mostra o formulário para criar uma nova unidade.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.unidades.create');
    }

    /**
     * Armazena uma nova unidade no banco de dados.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'uni_codigo' => 'required|string|max:50|unique:unidade,uni_codigo',
            'uni_descricao' => 'required|string|max:255',
            'uni_cidade' => 'required|string|max:100',
            'uni_uf' => 'required|string|size:2',
        ]);

        if ($validator->fails()) {
            return redirect()->route('unidades.create')
                ->withErrors($validator)
                ->withInput();
        }

        Unidade::create($request->all());

        return redirect()->route('unidades.index')
            ->with('success', 'Unidade criada com sucesso!');
    }

    /**
     * Exibe uma unidade específica.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $unidade = Unidade::findOrFail($id);
        return view('unidades.show', compact('unidade'));
    }

    /**
     * Atualiza uma unidade específica no banco de dados.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $unidade = Unidade::findOrFail($id);
        
        $validator = Validator::make($request->all(), [
            'uni_codigo' => 'required|string|max:50|unique:unidade,uni_codigo,' . $id . ',uni_id',
            'uni_descricao' => 'required|string|max:255',
            'uni_cidade' => 'required|string|max:100',
            'uni_uf' => 'required|string|size:2',
        ]);

        if ($validator->fails()) {
            return redirect()->route('unidades.edit', $id)
                ->withErrors($validator)
                ->withInput();
        }

        $unidade->update($request->all());

        return redirect()->route('unidades.index')
            ->with('success', 'Unidade atualizada com sucesso!');
    }

    /**
     * Remove uma unidade específica do banco de dados.
     * Realiza exclusão em cascata de users e selfs associados.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $unidade = Unidade::findOrFail($id);
        
        // Excluir todos os selfs associados
        $unidade->selfs()->delete();
        
        // Excluir todos os usuários associados
        $unidade->users()->delete();
        
        // Finalmente excluir a unidade
        $unidade->delete();

        return redirect()->route('unidades.index')
            ->with('success', 'Unidade excluída com sucesso!');
    }

    /**
     * Busca unidades com base em critérios de filtro.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function search(Request $request)
    {
        $query = Unidade::query();

        if ($request->has('codigo') && !empty($request->codigo)) {
            $query->where('uni_codigo', 'like', '%' . $request->codigo . '%');
        }

        if ($request->has('descricao') && !empty($request->descricao)) {
            $query->where('uni_descricao', 'like', '%' . $request->descricao . '%');
        }

        if ($request->has('cidade') && !empty($request->cidade)) {
            $query->where('uni_cidade', 'like', '%' . $request->cidade . '%');
        }

        if ($request->has('uf') && !empty($request->uf)) {
            $query->where('uni_uf', $request->uf);
        }

        $unidades = $query->get();

        return view('unidades.index', compact('unidades'));
    }

    /**
     * Lista todos os usuários associados a uma unidade.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function usuarios($id)
    {
        $unidade = Unidade::findOrFail($id);
        $usuarios = $unidade->todosUsuarios;
        
        return view('unidades.usuarios', compact('unidade', 'usuarios'));
    }
    // Adicione estes métodos ao seu UnidadeController.php

    /**
     * Mostra o formulário para editar uma unidade específica com selfs e usuários disponíveis.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $unidade = Unidade::findOrFail($id);
        
        // Busca todos os selfs que não estão associados à unidade
        $selfsDisponiveis = \App\Models\Selfs::whereNotIn('sel_id', function($query) use ($id) {
            $query->select('sel_id')
                ->from('selfs')
                ->where('sel_uni_id', $id);
        })->get();
        
        // Busca todos os usuários que não estão associados à unidade através da tabela units
        $usuariosAssociados = \App\Models\Unit::where('unit_uni_id', $id)->pluck('unit_use_id')->toArray();
        $usuariosDisponiveis = \App\Models\User::whereNotIn('use_id', $usuariosAssociados)->get();
        
        return view('admin.unidades.edit', compact('unidade', 'selfsDisponiveis', 'usuariosDisponiveis'));
    }

    /**
     * Adiciona um usuário à unidade.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function addUsuario(Request $request, $id)
    {
        $unidade = Unidade::findOrFail($id);
        $userId = $request->input('user_id');
        
        try {
            // Verificar se o vínculo já existe para evitar duplicidade
            $existingUnit = \App\Models\Unit::where('unit_uni_id', $id)
                ->where('unit_use_id', $userId)
                ->first();
                
            if (!$existingUnit) {
                // Cria o vínculo na tabela units
                \App\Models\Unit::create([
                    'unit_uni_id' => $id,
                    'unit_use_id' => $userId
                ]);
            }
            
            if ($request->ajax()) {
                return response()->json(['success' => true]);
            }
            
            return redirect()->route('unidades.edit', $id)
                ->with('success', 'Usuário associado com sucesso!');
        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => $e->getMessage()]);
            }
            
            return redirect()->route('unidades.edit', $id)
                ->with('error', 'Erro ao associar usuário: ' . $e->getMessage());
        }
    }

    /**
     * Remove um usuário da unidade.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function removeUsuario(Request $request, $id)
    {
        $userId = $request->input('user_id');
        
        try {
            // Remove o vínculo da tabela units
            \App\Models\Unit::where('unit_uni_id', $id)
                ->where('unit_use_id', $userId)
                ->delete();
            
            if ($request->ajax()) {
                return response()->json(['success' => true]);
            }
            
            return redirect()->route('unidades.edit', $id)
                ->with('success', 'Usuário removido com sucesso!');
        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => $e->getMessage()]);
            }
            
            return redirect()->route('unidades.edit', $id)
                ->with('error', 'Erro ao remover usuário: ' . $e->getMessage());
        }
    }
}