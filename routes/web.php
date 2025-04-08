<?php

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

Route::get('/', function () {
    return view('auth.login');
});

Route::get('/home', function () {
    return view('home');
})->middleware(['auth'])->name('home');

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::get('/logout', function () {
    Auth::logout();
    return redirect('/login');
})->name('logout');

// Rotas protegidas por autenticação
Route::middleware(['auth'])->group(function () {

    // ------------------------- Unidades ------------------------------------
    Route::resource('unidades', App\Http\Controllers\Admin\UnidadeController::class);
    Route::get('unidades/search', [App\Http\Controllers\Admin\UnidadeController::class, 'search'])->name('unidades.search');
    Route::get('unidades/{unidade}/usuarios', [App\Http\Controllers\Admin\UnidadeController::class, 'usuarios'])->name('unidades.usuarios');
    // Rotas para gerenciar usuários nas unidades
    Route::post('unidades/{unidade}/add-usuario', [App\Http\Controllers\Admin\UnidadeController::class, 'addUsuario'])->name('unidades.add.usuario');
    Route::delete('unidades/{unidade}/remove-usuario', [App\Http\Controllers\Admin\UnidadeController::class, 'removeUsuario'])->name('unidades.remove.usuario');

    // ------------------------- Selfs ------------------------------------
    Route::resource('selfs', App\Http\Controllers\Admin\SelfsController::class);
    Route::get('selfs/search', [App\Http\Controllers\Admin\SelfsController::class, 'search'])->name('selfs.search');
    Route::post('selfs/{self}/toggle-status', [App\Http\Controllers\Admin\SelfsController::class, 'toggleStatus'])->name('selfs.toggle-status');

    // ------------------------- Roles ------------------------------------
    Route::resource('roles', App\Http\Controllers\Admin\RoleController::class);
    Route::get('roles/search', [App\Http\Controllers\Admin\RoleController::class, 'search'])->name('roles.search');
    Route::get('roles/{id}/users', [App\Http\Controllers\Admin\RoleController::class, 'getUsers'])->name('roles.users');

    // ------------------------- Users ------------------------------------
    Route::resource('users', App\Http\Controllers\Admin\UserController::class);
    Route::get('users/search', [App\Http\Controllers\Admin\UserController::class, 'search'])->name('users.search');
    Route::get('users/{id}/unidades', [App\Http\Controllers\Admin\UserController::class, 'unidades'])->name('users.unidades');
    Route::post('users/{id}/add-unidade', [App\Http\Controllers\Admin\UserController::class, 'addUnidade'])->name('users.add-unidade');
    Route::delete('users/{id}/remove-unidade', [App\Http\Controllers\Admin\UserController::class, 'removeUnidade'])->name('users.remove-unidade');
    Route::post('users/{user}/toggle-status', [App\Http\Controllers\Admin\UserController::class, 'toggleStatus'])->name('users.toggle-status');
});
