<?php

namespace App\Http\Controllers\Admin;

use App\Events\Admin\Selfs\Create;
use App\Events\Admin\Selfs\Edit;
use App\Events\Admin\Selfs\Delete;
use App\Http\Controllers\Controller;
use App\Models\Selfs;
use App\Models\Unidade;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Crypt;

class SelfsController extends Controller
{
    public function index()
    {
        $selfs = Selfs::with('unidade.tipoUnidade')->orderBy('sel_id')->get();
        return view("admin.selfs.index", compact("selfs"));
    }

    public function create()
    {
        $unidades = Unidade::orderBy('uni_codigo')->get();
        
        return view("admin.selfs.create", compact("unidades"));
    }

    public function store(Request $request)
    {
        $request->validate([
            'sel_name' => 'required|string|max:255',
            'sel_pdv_ip' => 'required|ipv4',
            'sel_dvr_ip' => 'required|ipv4',
            'sel_dvr_username' => 'required|string|max:255',
            'sel_dvr_password' => 'required|string|max:255',
            'sel_camera_canal' => 'required|string|max:255',
            'sel_dvr_porta' => 'required|numeric|max:65535',
            'sel_rtsp_path' => 'required|string|max:250',
            'sel_uni_id' => 'required|exists:unidade,uni_id',
            'sel_pdv_codigo' => 'required|string|max:3',
        ], [
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
        ]);

        DB::beginTransaction();

        try {
            $selfsData = [
                'sel_name' => $request->sel_name,
                'sel_pdv_ip' => $request->sel_pdv_ip,
                'sel_dvr_ip' => $request->sel_dvr_ip,
                'sel_dvr_username' => $request->sel_dvr_username,
                'sel_dvr_password' => $request->sel_dvr_password,
                'sel_camera_canal' => $request->sel_camera_canal,
                'sel_dvr_porta' => $request->sel_dvr_porta,
                'sel_rtsp_path' => $request->sel_rtsp_path,
                'sel_uni_id' => $request->sel_uni_id,
                'sel_status' => $request->has('sel_status'),
                'sel_pdv_codigo' => $request->sel_pdv_codigo,
            ];
            
            $selfs = Selfs::create($selfsData);
            
            DB::commit();
            
            event(new Create($selfs->sel_id, request()->ip()));

            return redirect()->route("admin.selfs.index")
                ->with("success", "SelfCheckout criado com sucesso.");
        } catch (\Exception $e) {
            DB::rollBack();
            
            // Log::error('Erro ao criar SelfCheckout: ' . $e->getMessage());
            
            return redirect()->back()
                ->with("error", "Erro ao cadastrar o SelfCheckout: " . $e->getMessage())
                ->withInput();
        }
    }

    public function show($id)
    {
    
    }

    public function edit($id)
    {
        $selfs = Selfs::findOrFail($id);
        $unidades = Unidade::orderBy('uni_codigo')->get();
        
        return view("admin.selfs.edit", compact("selfs", "unidades"));
    }

    public function update(Request $request, $id)
    {
        $selfs = Selfs::findOrFail($id);
        
        $request->validate([
            'sel_name' => 'required|string|max:255',
            'sel_pdv_ip' => 'required|ipv4',
            'sel_dvr_ip' => 'required|ipv4',
            'sel_dvr_username' => 'required|string|max:255',
            'sel_dvr_password' => 'required|string|max:255',
            'sel_camera_canal' => 'required|string|max:255',
            'sel_dvr_porta' => 'required|numeric|max:65535',
            'sel_rtsp_path' => 'required|string|max:250',
            'sel_uni_id' => 'required|exists:unidade,uni_id',
            'sel_pdv_codigo' => 'required|string|max:3',
        ], [
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
        ]);

        DB::beginTransaction();
        
        try {
            $selfsData = [
                'sel_name' => $request->sel_name,
                'sel_pdv_ip' => $request->sel_pdv_ip,
                'sel_dvr_ip' => $request->sel_dvr_ip,
                'sel_dvr_username' => $request->sel_dvr_username,
                'sel_dvr_password' => $request->sel_dvr_password,
                'sel_camera_canal' => $request->sel_camera_canal,
                'sel_dvr_porta' => $request->sel_dvr_porta,
                'sel_rtsp_path' => $request->sel_rtsp_path,
                'sel_uni_id' => $request->sel_uni_id,
                'sel_status' => $request->has('sel_status'),
                'sel_pdv_codigo' => $request->sel_pdv_codigo,
            ];
            
            $selfs->update($selfsData);
            
            DB::commit();
            
            event(new Edit($selfs->sel_id, request()->ip()));

            return redirect()->route("admin.selfs.index")
                ->with("success", "SelfCheckout atualizado com sucesso.");
        } catch (\Exception $e) {
            DB::rollBack();
            
            // Log::error('Erro ao atualizar SelfCheckout: ' . $e->tMessage());
            
            return redirect()->back()
                ->with("error", "Erro ao atualizar o SelfCheckout: " . $e->getMessage())
                ->withInput();
        }
    }

    public function destroy($id)
    {
        DB::beginTransaction();
        
        try {
            $selfs = Selfs::findOrFail($id);
            $selfsName = $selfs->sel_name;
            $selfsUniId = $selfs->sel_uni_id;
            
            $selfs->delete();
            
            DB::commit();
            
            event(new Delete("NOME SelfCheckout: {$selfsName} ID - Unidade: {$selfsUniId}", request()->ip()));
            
            return redirect()->route("admin.selfs.index")
                ->with("success", "selfCheckout excluído com sucesso.");
        } catch (\Exception $e) {
            DB::rollBack();
            
            // Log::error('Erro ao excluir SelfCheckout: ' . $e->getMessage());
            
            return redirect()->route("admin.selfs.index")
                ->with("error", "Erro ao excluir o SelfCheckout: " . $e->getMessage());
        }
    }
}