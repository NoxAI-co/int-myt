<?php

namespace App\Http\Controllers;

use App\AsignarMaterial;
use App\Model\Ingresos\ItemsAsignarMaterial;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class InventarioTecnicosController extends Controller
{
    public function __construct(){
        $this->middleware('auth');
        view()->share(['seccion' => 'Inventario de Técnicos', 'title' => 'Inventario de Técnicos', 'icon' =>'fas fa-plus', 'subseccion' => 'inventario']);
    }

    public function index(Request $request){
        $this->getAllPermissions(Auth::user()->id);
        $tecnicos = User::where('rol',4)->get();
        return view('inventario-tecnicos.index')->with(compact('tecnicos'));
    }

    public function show($id_tecnico, $type, $group)
    {
        $this->getAllPermissions(Auth::user()->id);
        $materials = [];
        $tecnico = User::find($id_tecnico);

        if($type == "ingresos"){
            if($group == "agrupar"){
                $materials = ItemsAsignarMaterial::with('material')
                    ->join('asignacion_materials', 'items_asignar_materials.id_asignacion_material', '=', 'asignacion_materials.id')
                    ->select('id_material', DB::raw('SUM(cantidad) as total_cantidad'))
                    ->where('asignacion_materials.id_tecnico', $id_tecnico)
                    ->groupBy('id_material')
                    ->get();
            }else{
                $materials = ItemsAsignarMaterial::with(['material', 'asignacion'])
                    ->whereHas('asignacion', function($query) use ($id_tecnico) {
                        $query->where('id_tecnico', $id_tecnico);
                    })
                    ->select('id_material', 'cantidad', 'created_at')
                    ->get();
            }
        }
        return view('inventario-tecnicos.show')->with(compact('tecnico','type', 'group', 'materials'));
    }
}