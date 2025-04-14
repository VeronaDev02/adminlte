<?php

use App\Http\Controllers\Auth\LoginController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
Auth::routes();

Route::get('/', function () {
    return view('auth.login');
});


// Rota principal do dashboard - usando HomeController simplificado
Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::post('/logout', [LoginController::class, 'logout'])
    ->name('logout')
    ->middleware('auth');

// Rotas protegidas por autenticação
Route::middleware(['auth:web', 'verifica.permissions.users'])->group(function () {

    // ------------------------- Unidades ------------------------------------
    Route::resource('unidades', App\Http\Controllers\Admin\UnidadeController::class);
    Route::get('unidades/search', [App\Http\Controllers\Admin\UnidadeController::class, 'search'])->name('unidades.search');
    Route::get('unidades/{unidade}/usuarios', [App\Http\Controllers\Admin\UnidadeController::class, 'usuarios'])->name('unidades.usuarios');
    // Rotas para gerenciar usuários nas unidades
    Route::post('unidades/{id}/processar-usuarios', [App\Http\Controllers\Admin\UnidadeController::class, 'processarUsuarios'])
    ->name('unidades.process-usuarios');

    // ------------------------- Selfs ------------------------------------
    Route::resource('selfs', App\Http\Controllers\Admin\SelfsController::class);
    Route::get('selfs/search', [App\Http\Controllers\Admin\SelfsController::class, 'search'])->name('selfs.search');
    Route::post('selfs/{self}/toggle-status', [App\Http\Controllers\Admin\SelfsController::class, 'toggleStatus'])->name('selfs.toggle-status');

    // ------------------------- Roles ------------------------------------
    Route::resource('roles', App\Http\Controllers\Admin\RoleController::class);
    Route::get('roles/search', [App\Http\Controllers\Admin\RoleController::class, 'search'])->name('roles.search');
    Route::get('roles/{id}/users', [App\Http\Controllers\Admin\RoleController::class, 'getUsers'])->name('roles.users');

    // ------------------------- API ------------------------------------
    Route::get('/users/get-funcionario', [App\Http\Controllers\Admin\UserController::class, 'getFuncionario'])->name('users.get-funcionario');

    // ------------------------- Users ------------------------------------
    Route::resource('users', App\Http\Controllers\Admin\UserController::class);
    Route::get('users/search', [App\Http\Controllers\Admin\UserController::class, 'search'])->name('users.search');
    Route::get('users/{id}/unidades', [App\Http\Controllers\Admin\UserController::class, 'unidades'])->name('users.unidades');
    Route::post('users/{id}/add-unidade', [App\Http\Controllers\Admin\UserController::class, 'addUnidade'])->name('users.add-unidade');
    Route::delete('users/{id}/remove-unidade', [App\Http\Controllers\Admin\UserController::class, 'removeUnidade'])->name('users.remove-unidade');
    Route::post('users/{user}/toggle-status', [App\Http\Controllers\Admin\UserController::class, 'toggleStatus'])->name('users.toggle-status');
    Route::post('users/{id}/processar-unidades', [App\Http\Controllers\Admin\UserController::class, 'processarUnidades'])
    ->name('users.processar-unidades');

    


});
Route::middleware("auth:web")->group(function () {
    // Rota para a página de perfil
    Route::get("perfil", [
        App\Http\Controllers\User\UserController::class, 
        "getProfile"
    ])->name("user.profile");
    
    // Rota para adicionar imagem de usuário
    Route::post("addImgUser", [
        App\Http\Controllers\User\UserController::class, 
        "addImgUser"
    ])->name("addImgUser");
    
    // Rota GET para a página de redefinir senha (quando status_password = 0)
    Route::get("redefinirSenha", [
        App\Http\Controllers\User\UserController::class, // Corrigido aqui
        "redefinirSenha",
    ])->name("user.redefinirSenhaPage");
    
    // Rota PUT para processar a redefinição de senha (quando status_password = 0)
    Route::put("redefinirSenha", [
        App\Http\Controllers\User\UserController::class,
        "redefinirSenhaPUT",
    ])->name("user.redefinirSenha");
    
    // Rota PUT para atualizar a senha pelo perfil
    Route::put("redefinirSenhaPerfil", [
        App\Http\Controllers\User\UserController::class,
        "redefinirSenhaPerfil",
    ])->name("user.redefinirSenhaPerfil");
});