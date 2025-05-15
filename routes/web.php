<?php

use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Auth\LoginController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\ApiSSHController;

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


Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::post('/logout', [LoginController::class, 'logout'])
    ->name('logout')
    ->middleware('auth');

// Rotas protegidas por autenticação
Route::middleware(['auth:web', 'verifica.permissions.users'])->group(function () {

    Route::resource("admin/users", UserController::class)
        ->names("admin.user");
    Route::post("getFuncionario", [
        UserController::class,
        "getFuncionario",
    ])->name("admin.user.getFuncionario");

    Route::resource("admin/roles", RoleController::class)
        ->names("admin.role");

    Route::resource("admin/unidades", App\Http\Controllers\Admin\UnidadeController::class)
        ->names("admin.unidade");

    Route::resource("admin/selfs", App\Http\Controllers\Admin\SelfsController::class)
        ->names("admin.selfs");

    Route::get('/admin/unidades/{unidadeId}/api-status', [ApiSSHController::class, 'checkApiStatus'])->name('admin.unidades.api.status');
    Route::post('/admin/unidades/{unidadeId}/toggle-api', [ApiSSHController::class, 'toggleApiStatus'])->name('admin.unidades.api.toggle');
    
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
        App\Http\Controllers\User\UserController::class,
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

    // ------------------------- Selfcheckout -------------------------
    Route::prefix('selfcheckout')->name('selfcheckout.')->group(function () {
        Route::get('/', [App\Http\Controllers\User\SelfsController::class, 'index'])->name('index');
    });

    Route::post('/save-ui-preferences', [App\Http\Controllers\User\UserController::class, 'saveThemePreferences'])
        ->name('user.save-ui-preferences');
    Route::post('/user/save-tela-preferences', [App\Http\Controllers\User\SelfsController::class, 'saveTelaPreferences'])
        ->name('user.saveTelaPreferences');
    Route::post('/user/destroy-tela-preferences/{index}', [App\Http\Controllers\User\SelfsController::class, 'destroyTelaPreferences'])
        ->name('destroy.tela.preferences');
        
    Route::get('/selfs/monitor', [App\Http\Controllers\User\SelfsController::class, 'show'])
        ->name('selfs.monitor');
    Route::delete('/menu/tela/{index}', [App\Http\Controllers\User\MenuController::class, 'deleteTela'])
        ->name('menu.deleteTela');
    
    // ------------------------- Rotas de Alertas -------------------------
    Route::post('/alertas/registrar', function (Illuminate\Http\Request $request) {
        $alerta = App\Models\AlertaLog::create([
            'alert_origin' => $request->input('pdv_codigo'),
        ]);
        
        return response()->json(['success' => true, 'alerta_id' => $alerta->id]);
    })->name('alertas.registrar');
    
    Route::post('/alertas/resolver', function (Illuminate\Http\Request $request) {
        $alerta = App\Models\AlertaLog::find($request->input('alerta_id'));
        
        if ($alerta) {
            $alerta->update([
                'alert_resolved_by' => auth()->id(),
                'closed_at' => now()
            ]);
            
            return response()->json(['success' => true]);
        }
        
        return response()->json(['success' => false, 'message' => 'Alerta não encontrado'], 404);
    })->name('alertas.resolver');
});