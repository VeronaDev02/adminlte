<?php

namespace App\Http\Controllers\User;

use Illuminate\Support\Facades\DB;
use App\Events\User\User\EditPassword;
use App\Events\User\User\EditPerfil;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Http\Requests\User\User\ResetPasswordRequest;
use App\Models\User;

class UserController extends Controller
{
    public function index()
    {
        $user = User::all();
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        //
    }

    public function show($id)
    {
        //
    }

    public function edit($id)
    {
        //
    }

    public function addImgUser(Request $request)
    {
        try {
            Auth::user()->update(["img_user" => $request->base64]);
            event(new EditPerfil(Auth::user()->id, request()->ip()));
            return redirect()
                ->back()
                ->with("success", "Imagem alterada com sucesso!");
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with("error", "Erro ao atualizar a imagem!");
        }
    }

    public function getProfile()
    {
        return view("user.user.user");
    }

    public function redefinirSenha()
    {
        return view("user.user.redefinirSenha");
    }

    public function redefinirSenhaPerfil(ResetPasswordRequest $request)
    {
        $user = Auth::user();

        if (!Hash::check($request->old_password, $user->use_password)) {
            return redirect()
                ->back()
                ->with("error", "A senha atual está incorreta!");
        }

        // Atualiza a senha diretamente sem o status_password
        $user->use_password = $request->new_password;
        $user->save();
        
        event(new EditPassword($user->use_id, request()->ip()));
        return redirect("home")->with(
            "success",
            "Senha redefinida com sucesso!"
        );
    }

    public function redefinirSenhaPUT(ResetPasswordRequest $request)
    {
        try {
            $user = Auth::user();
            
            // Atualiza a senha diretamente sem o status_password
            $user->use_password = $request->new_password;
            $user->save();
            
            event(new EditPassword($user->use_id, request()->ip()));
            
            // Forçar logout e redirecionar para login
            Auth::logout();
            return redirect()->route('login')->with(
                "success",
                "Senha redefinida com sucesso! Por favor, faça login novamente."
            );
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Ocorreu um erro ao redefinir a senha: ' . $e->getMessage());
        }
    }

    public function update(Request $request, $id)
    {
        //
    }

    public function destroy($id)
    {
        //
    }
}
