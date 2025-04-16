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
    Route::prefix('unidades')->group(function () {
        // Rota para listar todas as unidades
        Route::get('/', [App\Http\Controllers\Admin\UnidadeController::class, 'index'])
            ->name('unidades.index');
        
        // Rotas para criar, editar e visualizar unidades individuais
        Route::get('/create', [App\Http\Controllers\Admin\UnidadeController::class, 'create'])
            ->name('unidades.create');
        Route::get('/{id}/edit', [App\Http\Controllers\Admin\UnidadeController::class, 'edit'])
            ->name('unidades.edit');
        Route::get('/{id}/usuarios', [App\Http\Controllers\Admin\UnidadeController::class, 'usuarios'])
            ->name('unidades.usuarios');
    });

    // ------------------------- Tipo de Unidades ------------------------------------
    Route::prefix('tipo-unidade')->group(function () {
        // Listar unidades por tipo
        Route::get('/{codigo}', [App\Http\Controllers\Admin\UnidadeController::class, 'porTipo'])
            ->name('tipo-unidade.unidades');
        
        // Criar unidade para um tipo específico
        Route::get('/{codigo}/create', [App\Http\Controllers\Admin\UnidadeController::class, 'createPorTipo'])
            ->name('tipo-unidade.create');
        
        // Editar unidade para um tipo específico
        Route::get('/{codigo}/edit/{unidade}', [App\Http\Controllers\Admin\UnidadeController::class, 'editPorTipo'])
            ->name('tipo-unidade.edit');
    });

    // ------------------------- Selfs ------------------------------------
    Route::prefix('selfs')->name('selfs.')->group(function () {
        Route::get('/', App\Http\Livewire\Selfs\SelfCheckoutList::class)->name('index');
        Route::get('/create', App\Http\Livewire\Selfs\SelfCheckoutForm::class)->name('create');
        Route::get('/{self}/edit', App\Http\Livewire\Selfs\SelfCheckoutForm::class)->name('edit');
    });
    // ------------------------- Roles ------------------------------------
    Route::resource('roles', App\Http\Controllers\Admin\RoleController::class);
    Route::get('roles/search', [App\Http\Controllers\Admin\RoleController::class, 'search'])->name('roles.search');
    Route::get('roles/{id}/users', [App\Http\Controllers\Admin\RoleController::class, 'getUsers'])->name('roles.users');

    // ------------------------- API ------------------------------------
    Route::get('/users/get-funcionario', [App\Http\Controllers\Admin\UserController::class, 'getFuncionario'])->name('users.get-funcionario');

    // ------------------------- Users ------------------------------------
    Route::resource('users', App\Http\Controllers\Admin\UserController::class);
    Route::get('users/search', [App\Http\Controllers\Admin\UserController::class, 'search'])->name('users.search');
    Route::get('users/get-funcionario', [App\Http\Controllers\Admin\UserController::class, 'getFuncionario'])->name('users.get-funcionario');
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