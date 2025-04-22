<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\Selfs;
use App\Models\Unidade;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $isAdmin = Auth::user()->role && Auth::user()->role->rol_id === 1 && Auth::user()->use_active == true;
        
        if ($isAdmin) {
            $data = $this->getAdminDashboardData();
            return view('admin.dashboard', $data);
        } else {
            return view('home');
        }
    }
    
    private function getAdminDashboardData()
    {
        // Contagem básica de entidades
        $totalUsers = User::count();
        $totalUnits = Unidade::count();
        $totalSelfs = Selfs::count();
        $totalRoles = Role::count();
        
        $activeUsers = User::where('use_active', true)->count();
        
        $activeSelfs = Selfs::where('sel_status', true)->count();
        $inactiveSelfs = Selfs::where('sel_status', false)->count();
        
        $roles = Role::withCount('users')->get();
        $roleLabels = $roles->pluck('rol_name')->toArray();
        $roleData = $roles->pluck('users_count')->toArray();
        
        $usersWithoutRole = User::whereNull('use_rol_id')->count();
        if ($usersWithoutRole > 0) {
            $roleLabels[] = 'Sem Função';
            $roleData[] = $usersWithoutRole;
        }
        
        $selfsUnitQuery = DB::table('unidade')
            ->select('unidade.uni_codigo', DB::raw('COUNT(selfs.sel_id) as selfs_count'))
            ->leftJoin('selfs', 'unidade.uni_id', '=', 'selfs.sel_uni_id')
            ->groupBy('unidade.uni_id', 'unidade.uni_codigo')
            ->whereNotNull('selfs.sel_id')
            ->orderBy('selfs_count', 'desc')
            ->limit(10);
            
        $selfsUnits = $selfsUnitQuery->get();
        $selfsUnitLabels = $selfsUnits->pluck('uni_codigo')->toArray();
        $selfsUnitData = $selfsUnits->pluck('selfs_count')->toArray();
        
        $userUnitQuery = DB::table('unidade')
            ->select('unidade.uni_codigo', DB::raw('COUNT(DISTINCT units.unit_use_id) as user_count'))
            ->leftJoin('units', 'unidade.uni_id', '=', 'units.unit_uni_id')
            ->whereNotNull('units.unit_use_id')
            ->groupBy('unidade.uni_id', 'unidade.uni_codigo')
            ->orderBy('user_count', 'desc')
            ->limit(10);
            
        $userUnits = $userUnitQuery->get();
        $userUnitLabels = $userUnits->pluck('uni_codigo')->toArray();
        $userUnitData = $userUnits->pluck('user_count')->toArray();
        
        $userUnidades = Auth::user()->unidades()->pluck('uni_codigo')->implode(', ');
        
        return compact(
            'totalUsers', 'totalUnits', 'totalSelfs', 'totalRoles',
            'activeUsers', 'activeSelfs', 'inactiveSelfs',
            'roleLabels', 'roleData',
            'selfsUnitLabels', 'selfsUnitData',
            'userUnitLabels', 'userUnitData',
            'userUnidades'
        );
    }
}