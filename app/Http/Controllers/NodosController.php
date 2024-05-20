<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Carbon\Carbon;  
use Mail; 
use Validator;
use Illuminate\Validation\Rule;  
use Auth; 
use DB;
use Session;

use App\Mikrotik;
use App\User;
use App\Contrato;
use App\Nodo;
use App\Campos;

class NodosController extends Controller
{
    public function __construct(){
        $this->middleware('auth');
        set_time_limit(300);
        view()->share(['inicio' => 'master', 'seccion' => 'zonas', 'subseccion' => 'nodos', 'title' => 'Nodos', 'icon' =>'fas fa-sitemap']);
    }
    
    public function index(Request $request){
        $this->getAllPermissions(Auth::user()->id);
        return view('nodos.index');
    }

    public function nodos(Request $request){
        $modoLectura = auth()->user()->modo_lectura();
        $nodos = Nodo::query()
            ->where('empresa', Auth::user()->empresa);

        if ($request->filtro == true) {
            if($request->nro){
                $nodos->where(function ($query) use ($request) {
                    $query->orWhere('nro', 'like', "%{$request->nro}%");
                });
            }
            if($request->nombre){
                $nodos->where(function ($query) use ($request) {
                    $query->orWhere('nombre', 'like', "%{$request->nombre}%");
                });
            }
            if($request->status >=0){
                $nodos->where(function ($query) use ($request) {
                    $query->orWhere('status', 'like', "%{$request->status}%");
                });
            }
        }

        return datatables()->eloquent($nodos)
            ->editColumn('nro', function (Nodo $nodo) {
                return "<a href=" . route('nodos.show', $nodo->id) . ">{$nodo->nro}</div></a>";
            })
            ->editColumn('nombre', function (Nodo $nodo) {
                return "<a href=" . route('nodos.show', $nodo->id) . ">{$nodo->nombre}</div></a>";
            })
            ->editColumn('status', function (Nodo $nodo) {
                return "<span class='text-{$nodo->status("true")}'><strong>{$nodo->status()}</strong></span>";
            })
            ->addColumn('acciones', $modoLectura ?  "" : "nodos.acciones")
            ->rawColumns(['acciones', 'nombre', 'nro', 'status'])
            ->toJson();
    }

    public function create(){
        $this->getAllPermissions(Auth::user()->id);
        view()->share(['title' => 'Nuevo Nodo']);

        return view('nodos.create');
    }
    
    public function store(Request $request){
        $nro = 0;
        $nodo = Nodo::where('id', '>', 0)->where('empresa', Auth::user()->empresa)->orderBy('created_at', 'desc')->first();
        
        if($nodo){
            $nro = $nodo->nro + 1;
        }
        
        $nodo = new Nodo;
        $nodo->nro = $nro++;
        $nodo->nombre = $request->nombre;
        $nodo->status = $request->status;
        $nodo->descripcion = $request->descripcion;
        $nodo->created_by = Auth::user()->id;
        $nodo->empresa = Auth::user()->empresa;
        $nodo->save();

        $mensaje='SE HA CREADO SATISFACTORIAMENTE EL NODO';
        return redirect('empresa/nodos')->with('success', $mensaje);
    }

    public function show($id){
        $this->getAllPermissions(Auth::user()->id);
        $nodo = Nodo::where('id', $id)->where('empresa', Auth::user()->empresa)->first();
        $tabla = Campos::where('modulo', 2)->where('estado', 1)->where('empresa', Auth::user()->empresa)->orderBy('orden', 'asc')->get();

        if ($nodo) {
            $contratos = Contrato::where('nodo', $nodo->id)->get();
            view()->share(['title' => $nodo->nombre]);
            return view('nodos.show')->with(compact('nodo', 'contratos','tabla'));
        }
        return redirect('empresa/nodos')->with('danger', 'NODO NO ENCONTRADO, INTENTE NUEVAMENTE');
    }
    
    public function edit($id){
        $this->getAllPermissions(Auth::user()->id);
        $nodo = Nodo::where('id', $id)->where('empresa', Auth::user()->empresa)->first();
        
        if ($nodo) {
            view()->share(['title' => 'Editar Nodo: '.$nodo->nombre]);

            return view('nodos.edit')->with(compact('nodo'));
        }
        return redirect('empresa/nodos')->with('danger', 'NODO NO ENCONTRADO, INTENTE NUEVAMENTE');
    }

    public function update(Request $request, $id){
        $nodo = Nodo::where('id', $id)->where('empresa', Auth::user()->empresa)->first();
        
        if ($nodo) {
            $nodo->nombre = $request->nombre;
            $nodo->status = $request->status;
            $nodo->descripcion = $request->descripcion;
            $nodo->updated_by = Auth::user()->id;
            $nodo->save();
            
            $mensaje='SE HA MODIFICADO SATISFACTORIAMENTE EL NODO';
            return redirect('empresa/nodos')->with('success', $mensaje);
        }
        return redirect('empresa/nodos')->with('danger', 'CLIENTE NO ENCONTRADO, INTENTE NUEVAMENTE');
    }
    
    public function destroy($id){
        $nodo = Nodo::where('id', $id)->where('empresa', Auth::user()->empresa)->first();
        
        if($nodo){
            $nodo->delete();
            $mensaje = 'SE HA ELIMINADO EL NODO CORRECTAMENTE';
            return redirect('empresa/nodos')->with('success', $mensaje);
        }else{
            return redirect('empresa/nodos')->with('danger', 'NODO NO ENCONTRADO, INTENTE NUEVAMENTE');
        }
    }
    
    public function act_des($id){
        $nodo = Nodo::where('id', $id)->where('empresa', Auth::user()->empresa)->first();
        
        if($nodo){
            if($nodo->status == 0){
                $nodo->status = 1;
                $mensaje = 'SE HA HABILITADO EL NODO CORRECTAMENTE';
            }else{
                $nodo->status = 0;
                $mensaje = 'SE HA DESHABILITADO EL NODO CORRECTAMENTE';
            }
            $nodo->save();
            return redirect('empresa/nodos')->with('success', $mensaje);
        }else{
            return redirect('empresa/nodos')->with('danger', 'NODO NO ENCONTRADO, INTENTE NUEVAMENTE');
        }
    }

    public function state_lote($nodos, $state){
        $this->getAllPermissions(Auth::user()->id);

        $succ = 0; $fail = 0;

        $nodos = explode(",", $nodos);

        for ($i=0; $i < count($nodos) ; $i++) {
            $nodo = Nodo::find($nodos[$i]);

            if($nodo){
                if($state == 'disabled'){
                    $nodo->status = 0;
                }elseif($state == 'enabled'){
                    $nodo->status = 1;
                }
                $nodo->save();
                $succ++;
            }else{
                $fail++;
            }
        }

        return response()->json([
            'success'   => true,
            'fallidos'  => $fail,
            'correctos' => $succ,
            'state'     => $state
        ]);
    }

    public function destroy_lote($nodos){
        $this->getAllPermissions(Auth::user()->id);

        $succ = 0; $fail = 0;

        $nodos = explode(",", $nodos);

        for ($i=0; $i < count($nodos) ; $i++) {
            $nodo = Nodo::find($nodos[$i]);
            if ($nodo->uso()==0) {
                $nodo->delete();
                $succ++;
            } else {
                $fail++;
            }
        }

        return response()->json([
            'success'   => true,
            'fallidos'  => $fail,
            'correctos' => $succ,
            'state'     => 'eliminados'
        ]);
    }
}
