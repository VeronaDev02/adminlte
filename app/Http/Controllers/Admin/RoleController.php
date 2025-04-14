<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Role;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    public function index()
    {
        return view('admin.roles.index');
    }

    public function create()
    {
        return view('admin.roles.create');
    }

    public function edit($id)
    {
        return view('admin.roles.edit', ['roleId' => $id]);
    }

    public function show($id)
    {
        $role = Role::findOrFail($id);
        return view('admin.roles.show', compact('role'));
    }

    public function search(Request $request)
    {
        $query = Role::query();

        if ($request->has('name') && !empty($request->name)) {
            $query->where('rol_name', 'like', '%' . $request->name . '%');
        }

        $roles = $query->get();

        return view('admin.roles.index', compact('roles'));
    }

    public function usuarios($id)
    {
        $role = Role::findOrFail($id);
        $usuarios = $role->users;
        
        return view('admin.roles.usuarios', compact('role', 'usuarios'));
    }

    public function processarUsuarios(Request $request, $id)
    {
        return redirect()->route('roles.edit', $id)
            ->with('error', 'Processamento de usuários agora é feito pelo componente Livewire');
    }
}