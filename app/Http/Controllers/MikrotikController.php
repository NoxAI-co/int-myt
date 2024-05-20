<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use App\Empresa;

use Carbon\Carbon; use DB;
use App\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Validator;
use Illuminate\Validation\Rule;
use Auth;
use Session;
use App\Rules\guion;
use App\Contacto;
use App\Contrato;
use App\Numeracion;
use App\Mikrotik;
use App\PlanesVelocidad;
use App\Segmento;

include_once(app_path() .'/../public/routeros_api.class.php');
include_once(app_path() .'/../public/api_mt_include2.php');

use routeros_api;
use RouterosAPI;
use StdClass;
use App\Campos;

class MikrotikController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        view()->share(['seccion' => 'mikrotik', 'subseccion' => 'gestion_mikrotik', 'title' => 'Mikrotik', 'icon' =>'fas fa-server']);
    }

    public function index(){
      $this->getAllPermissions(Auth::user()->id);
      $tabla = Campos::join('campos_usuarios', 'campos_usuarios.id_campo', '=', 'campos.id')->where('campos_usuarios.id_modulo', 15)->where('campos_usuarios.id_usuario', Auth::user()->id)->where('campos_usuarios.estado', 1)->orderBy('campos_usuarios.orden', 'ASC')->get();
      view()->share(['middel' => true]);
      return view('mikrotik.index')->with(compact('tabla'));
    }

    public function mikrotik(Request $request){
        $modoLectura = auth()->user()->modo_lectura();
        $mikrotiks = Mikrotik::query()
            ->where('empresa', Auth::user()->empresa);

        if ($request->filtro == true) {
            if($request->nombre){
                $mikrotiks->where(function ($query) use ($request) {
                    $query->orWhere('nombre', 'like', "%{$request->nombre}%");
                });
            }
            if($request->ip){
                $mikrotiks->where(function ($query) use ($request) {
                    $query->orWhere('ip', 'like', "%{$request->ip}%");
                });
            }
            if($request->puerto_web){
                $mikrotiks->where(function ($query) use ($request) {
                    $query->orWhere('puerto_web', 'like', "%{$request->puerto_web}%");
                });
            }
            if($request->puerto_api){
                $mikrotiks->where(function ($query) use ($request) {
                    $query->orWhere('puerto_api', 'like', "%{$request->puerto_api}%");
                });
            }
            if($request->puerto_winbox){
                $mikrotiks->where(function ($query) use ($request) {
                    $query->orWhere('puerto_winbox', 'like', "%{$request->puerto_winbox}%");
                });
            }
            if($request->interfaz){
                $mikrotiks->where(function ($query) use ($request) {
                    $query->orWhere('interfaz', 'like', "%{$request->interfaz}%");
                });
            }
            if($request->interfaz_lan){
                $mikrotiks->where(function ($query) use ($request) {
                    $query->orWhere('interfaz_lan', 'like', "%{$request->interfaz_lan}%");
                });
            }
            if($request->status){
                $status = ($request->status == 'A') ? 0 : $request->status;
                $mikrotiks->where(function ($query) use ($request, $status) {
                    $query->orWhere('status', $status);
                });
            }
        }

        return datatables()->eloquent($mikrotiks)
        ->editColumn('nombre', function (Mikrotik $mikrotik) {
            return "<a href=" . route('mikrotik.show', $mikrotik->id) . ">{$mikrotik->nombre}</a>";
        })
        ->editColumn('ip', function (Mikrotik $mikrotik) {
            return $mikrotik->ip;
        })
        ->editColumn('puerto_api', function (Mikrotik $mikrotik) {
            return $mikrotik->puerto_api;
        })
        ->editColumn('puerto_web', function (Mikrotik $mikrotik) {
            return $mikrotik->puerto_web;
        })
        ->editColumn('puerto_winbox', function (Mikrotik $mikrotik) {
            return $mikrotik->puerto_winbox;
        })
        ->editColumn('interfaz', function (Mikrotik $mikrotik) {
            return $mikrotik->interfaz;
        })
        ->editColumn('interfaz_lan', function (Mikrotik $mikrotik) {
            return $mikrotik->interfaz_lan;
        })
        ->editColumn('status', function (Mikrotik $mikrotik) {
            return "<span class='text-{$mikrotik->status("true")}'><strong>{$mikrotik->status()}</strong></span>";
        })
        ->editColumn('clientes_enabled', function (Mikrotik $mikrotik) {
            return '<span class="font-weight-bold text-success">'.$mikrotik->clientes('enabled').' clientes</span>';
        })
        ->editColumn('clientes_disabled', function (Mikrotik $mikrotik) {
            return '<span class="font-weight-bold text-danger">'.$mikrotik->clientes('disabled').' clientes</span>';
        })
        ->addColumn('acciones', $modoLectura ?  "" : "mikrotik.acciones")
        ->rawColumns(['acciones', 'nombre', 'clientes_enabled', 'clientes_disabled', 'status'])
        ->toJson();
    }

    public function create(){
        $this->getAllPermissions(Auth::user()->id);

        return view('mikrotik.create');
    }

    public function store(Request $request){
        $request->validate([
            'nombre' => 'required',
            'ip' => 'required',
            'usuario' => 'required',
            'clave' => 'required',
            'puerto_api' => 'required',
            'segmento_ip' => 'required',
            'interfaz' => 'required',
            'interfaz_lan' => 'required',
            'amarre_mac' => 'required'
        ]);

        $mikrotik = new Mikrotik;
        $mikrotik->nombre = $request->nombre;
        $mikrotik->ip = $request->ip;
        $mikrotik->puerto_api = $request->puerto_api;
        $mikrotik->puerto_web = $request->puerto_web;
        $mikrotik->puerto_winbox = $request->puerto_winbox;
        $mikrotik->usuario = $request->usuario;
        $mikrotik->clave = $request->clave;
        $mikrotik->interfaz = $request->interfaz;
        $mikrotik->interfaz_lan = $request->interfaz_lan;
        $mikrotik->created_by = Auth::user()->id;
        $mikrotik->empresa = Auth::user()->empresa;
        $mikrotik->amarre_mac = $request->amarre_mac;
        $mikrotik->save();

        for ($i = 0; $i < count($request->segmento_ip); $i++) {
            $segmento = new Segmento;
            $segmento->mikrotik = $mikrotik->id;
            $segmento->segmento = $request->segmento_ip[$i];
            $segmento->save();
        }

        $mensaje='Se ha creado satisfactoriamente el mikrotik';
        return redirect('empresa/mikrotik')->with('success', strtoupper($mensaje))->with('mikrotik_id', $mikrotik->id);
    }

    public function edit($id){
        $this->getAllPermissions(Auth::user()->id);
        $mikrotik = Mikrotik::where('id', $id)->where('empresa', Auth::user()->empresa)->first();

        if ($mikrotik) {
            $segmentos = Segmento::where('mikrotik', $mikrotik->id)->get();
            return view('mikrotik.edit')->with(compact('mikrotik', 'segmentos'));
        }
        return redirect('empresa/mikrotik')->with('danger', 'No existe un registro con ese id');
    }

    public function update(Request $request, $id){
        $mikrotik = Mikrotik::where('id', $id)->where('empresa', Auth::user()->empresa)->first();
        if ($mikrotik) {
            $request->validate([
                'nombre' => 'required',
                'ip' => 'required',
                'usuario' => 'required',
                'clave' => 'required',
                'puerto_api' => 'required',
                'interfaz' => 'required',
                'interfaz_lan' => 'required',
                'amarre_mac' => 'required'
            ]);

            $mikrotik->nombre = $request->nombre;
            $mikrotik->ip = $request->ip;
            $mikrotik->puerto_api = $request->puerto_api;
            $mikrotik->puerto_web = $request->puerto_web;
            $mikrotik->puerto_winbox = $request->puerto_winbox;
            $mikrotik->interfaz = $request->interfaz;
            $mikrotik->interfaz_lan = $request->interfaz_lan;
            $mikrotik->usuario = $request->usuario;
            $mikrotik->clave = $request->clave;
            $mikrotik->updated_by = Auth::user()->id;
            $mikrotik->status = 0;
            $mikrotik->amarre_mac = $request->amarre_mac;
            $mikrotik->save();

            $segmentos = Segmento::where('mikrotik', $mikrotik->id)->get();
            foreach($segmentos as $segmento){
                $segmento->delete();
            }

            for ($i = 0; $i < count($request->segmento_ip); $i++) {
                $segmento = new Segmento;
                $segmento->mikrotik = $mikrotik->id;
                $segmento->segmento = $request->segmento_ip[$i];
                $segmento->save();
            }

            $mensaje='Se ha modificado satisfactoriamente el Mikrotik';
            return redirect('empresa/mikrotik')->with('success', strtoupper($mensaje))->with('mikrotik_id', $mikrotik->id);
        }
        return redirect('empresa/mikrotik')->with('danger', 'No existe un registro con ese id');
    }

    public function destroy($id){
        $mikrotik = Mikrotik::where('id', $id)->where('empresa', Auth::user()->empresa)->first();
        if ($mikrotik) {
            $segmentos = Segmento::where('mikrotik', $mikrotik->id)->get();
            foreach($segmentos as $segmento){
                $segmento->delete();
            }
            $mikrotik->delete();

            return redirect('empresa/mikrotik')->with('success', strtoupper('Se ha eliminado correctamente el Mikrotik'));
        }
        return redirect('empresa/mikrotik')->with('danger', 'No existe un registro con ese id');
    }

    public function show($id){
        $this->getAllPermissions(Auth::user()->id);
        $mikrotik = Mikrotik::where('id', $id)->where('empresa', Auth::user()->empresa)->first();
        if ($mikrotik) {
            view()->share(['title' => 'Mikrotik: '.$mikrotik->nombre, 'icon' =>'fas fa-server', 'middel' => true]);
            $segmentos = Segmento::where('mikrotik', $mikrotik->id)->get();
            $tabla = Campos::where('modulo', 2)->where('estado', 1)->where('empresa', Auth::user()->empresa)->orderBy('orden', 'asc')->get();
            return view('mikrotik.show')->with(compact('mikrotik', 'segmentos', 'tabla'));
        }
        return redirect('empresa/mikrotik')->with('danger', 'No existe un registro con ese id');
    }

    public function conectar($id){
        $this->getAllPermissions(Auth::user()->id);
        $mikrotik = Mikrotik::where('id', $id)->where('empresa', Auth::user()->empresa)->first();
        if ($mikrotik->status == 0) {
            $API = new RouterosAPI();
            $API->port = $mikrotik->puerto_api;

            if ($API->connect($mikrotik->ip,$mikrotik->usuario,$mikrotik->clave)) {

                //$API->write('/ip/route/print');
                //$API->write('/ip/address/print');
                //$API->write("/interface/ethernet/getall", true);
                //$API->write("/tool/user-manager/user/getall", true);
                //$API->write("/system/identity/getall", true);

                // $API->write('/system/resource/print');
                // $READ = $API->read(false);
                // $ARRAY = $API->parseResponse($READ);

                // $API->write("/system/identity/getall", true);
                // $READ = $API->read(false);
                // $ARRAYS = $API->parseResponse($READ);

                $API->disconnect();

                //$mikrotik->nombre = $ARRAYS[0]['name'];
                // $mikrotik->board = $ARRAY[0]['board-name'];
                // $mikrotik->uptime = $ARRAY[0]['uptime'];
                // $mikrotik->cpu = $ARRAY[0]['cpu-load'];
                // $mikrotik->version = $ARRAY[0]['version'];
                // $mikrotik->buildtime = $ARRAY[0]['build-time'];
                // $mikrotik->freememory = $ARRAY[0]['free-memory'];
                // $mikrotik->totalmemory = $ARRAY[0]['total-memory'];
                // $mikrotik->cpucount = $ARRAY[0]['cpu-count'];
                // $mikrotik->cpufrequency = $ARRAY[0]['cpu-frequency'].' MHz';
                // $mikrotik->cpuload = $ARRAY[0]['cpu-load'].' %';
                // $mikrotik->freehddspace = $ARRAY[0]['free-hdd-space'];
                // $mikrotik->totalhddspace = $ARRAY[0]['total-hdd-space'];
                // $mikrotik->architecturename = $ARRAY[0]['architecture-name'];
                // $mikrotik->platform = $ARRAY[0]['platform'];
                $mikrotik->status = 1;
                $mikrotik->save();
                $mensaje='Conexión a la Mikrotik '.$mikrotik->nombre.' Realizada';
                $type = 'success';
            } else {
                $mikrotik->status = 0;
                $mikrotik->save();
                $mensaje='Conexión a la Mikrotik '.$mikrotik->nombre.' No Realizada';
                $type = 'danger';
            }
            return redirect('empresa/mikrotik')->with($type, $mensaje)->with('mikrotik_id', $mikrotik->id);
        }else{
            $mikrotik->status = 0;
            $mikrotik->save();

            $mensaje='La Mikrotik '.$mikrotik->nombre.' ha sido desconectada';
            $type = 'success';

            return redirect('empresa/mikrotik')->with($type, $mensaje)->with('mikrotik_id', $mikrotik->id);
        }
        return redirect('empresa/mikrotik')->with('danger', 'No existe un registro con ese id');
    }

    public function reglas($id){
        $this->getAllPermissions(Auth::user()->id);
        $mikrotik = Mikrotik::find($id);
        if ($mikrotik) {
            $API = new RouterosAPI();

            $API->port = $mikrotik->puerto_api;

            if ($API->connect($mikrotik->ip,$mikrotik->usuario,$mikrotik->clave)) {

                $API->comm("/ip/firewall/nat/add\n=action=redirect\n=chain=dstnat\n=comment='Manager - Suspension de ips (TCP)'\n=dst-port=!8291\n=protocol=tcp\n=src-address-list=morosos\n=to-ports=999");
                $API->comm("/ip/firewall/nat/add\n=action=redirect\n=chain=dstnat\n=comment='Manager - Suspender clientes(UDP)'\n=dst-port=!8291,53\n=protocol=udp\n=src-address-list=morosos\n=to-ports=999");
                $API->comm("/ip/proxy/set\n=enabled=yes\n=port=999");

                $API->disconnect();

                $mensaje='Reglas aplicadas satisfactoriamente a la Mikrotik '.$mikrotik->nombre;
                $type = 'success';
                $mikrotik->reglas = 1;
                $mikrotik->save();
            } else {
                $mensaje='Reglas no aplicadas a la Mikrotik '.$mikrotik->nombre.', intente nuevamente.';
                $type = 'danger';
            }
            return redirect('empresa/mikrotik')->with($type, $mensaje)->with('mikrotik_id', $mikrotik->id);
        }
        return redirect('empresa/mikrotik')->with('danger', 'No existe un registro con ese id');
    }

    public function importar($id){
        return back()->with('danger', 'FUNCIONALIDAD EN DESARROLLO');
        $this->getAllPermissions(Auth::user()->id);
        $mikrotik = Mikrotik::where('id', $id)->where('empresa', Auth::user()->empresa)->first();
        if ($mikrotik) {
            $API = new RouterosAPI();

            $API->port = $mikrotik->puerto_api;
            //$API->debug = true;

            if ($API->connect($mikrotik->ip,$mikrotik->usuario,$mikrotik->clave)) {
                $API->write('/interface/vlan/print');
                $READ = $API->read(false);
                $ARRAYS = $API->parseResponse($READ);

                $API->disconnect();
                $i=0;
                dd($ARRAYS);
                for ($i=0; $i <count($ARRAYS) ; $i++) {
                    dd($ARRAYS[$i]['mac-address']);
                }

                $plan = PlanesVelocidad::where('id', $request->plan_id)->first();
                $cliente = Contacto::find($request->client_id);

                $nro = Numeracion::where('empresa', 1)->first();
                $nro_contrato = $nro->contrato;

                while (true) {
                    $numero = Contrato::where('nro', $nro_contrato)->count();
                    if ($numero == 0) {
                        break;
                    }
                    $nro_contrato++;
                }

                $contrato = new Contrato();
                $contrato->plan_id                 = $request->plan_id;
                $contrato->nro                     = $nro_contrato;
                $contrato->client_id               = $request->client_id;
                $contrato->server_configuration_id = $mikrotik->id;
                $contrato->mac_address             = $array->mac-address;
                $contrato->fecha_corte             = $request->fecha_corte;
                $contrato->fecha_suspension        = $request->fecha_suspension;
                $contrato->usuario                 = $request->usuario;
                $contrato->password                = $request->password;
                $contrato->conexion                = $request->conexion;
                $contrato->interfaz                = $request->interfaz;
                $contrato->local_address           = $request->local_address;
                $contrato->mac_address             = $request->mac_address;
                $contrato->creador                 = Auth::user()->nombres;
                $contrato->save();

                $nro->contrato = $nro_contrato + 1;
                $nro->save();

                $mensaje='Se han importado 2345 contratos al sistema desde la mikrotik '.$mikrotik->nombre;
                $type = 'success';
            } else {
                $mensaje='No hemos podido conectar con la mikrotik '.$mikrotik->nombre.', intente nuevamente.';
                $type = 'danger';
            }
            return redirect('empresa/mikrotik')->with($type, $mensaje)->with('mikrotik_id', $mikrotik->id);
        }
        return redirect('empresa/mikrotik')->with('danger', 'No existe un registro con ese id');
    }

    public function log($id){
        $this->getAllPermissions(Auth::user()->id);
        $mikrotik = Mikrotik::where('id', $id)->where('empresa', Auth::user()->empresa)->first();
        if ($mikrotik) {
            view()->share(['title' => 'LOG Mikrotik: '.$mikrotik->nombre, 'icon' =>'fas fa-server']);
            return view('mikrotik.log')->with(compact('mikrotik'));
        }
        return redirect('empresa/mikrotik')->with('danger', 'No existe un registro con ese id');
    }

    public function logs(Request $request, $contrato){
        $modoLectura = auth()->user()->modo_lectura();
        $contratos = MovimientoLOG::query()
            ->where('empresa', Auth::user()->empresa);
        $contratos->where('log_movimientos.contrato', $contrato);

        return datatables()->eloquent($contratos)
            ->editColumn('created_at', function (MovimientoLOG $contrato) {
                return date('d-m-Y h:m:s A', strtotime($contrato->created_at));
            })
            ->editColumn('created_by', function (MovimientoLOG $contrato) {
                return $contrato->created_by()->nombres;
            })
            ->editColumn('descripcion', function (MovimientoLOG $contrato) {
                return $contrato->descripcion;
            })
            ->rawColumns(['created_at', 'created_by', 'descripcion'])
            ->toJson();
    }

    public function reiniciar($id){
        $this->getAllPermissions(Auth::user()->id);
        $mikrotik = Mikrotik::where('id', $id)->where('empresa', Auth::user()->empresa)->first();
        if ($mikrotik) {
            $API = new RouterosAPI();

            $API->port = $mikrotik->puerto_api;
            //$API->debug = true;

            if ($API->connect($mikrotik->ip,$mikrotik->usuario,$mikrotik->clave)) {
                $API->write("/system/reboot");
                $API->read();
                if($API){
                    $mensaje='El Mikrotik '.$mikrotik->nombre.' ha sido reiniciado';
                    $type = 'success';
                }else{
                    $mensaje='ERROR: No hemos podido reiniciar el Mikrotik '.$mikrotik->nombre;
                    $type = 'danger';
                }
                $API->disconnect();
            } else {
                $mensaje='ERROR: No hemos podido reiniciar el Mikrotik '.$mikrotik->nombre;
                $type = 'danger';
            }
            return redirect('empresa/mikrotik')->with($type, $mensaje)->with('mikrotik_id', $mikrotik->id);
        }
        return redirect('empresa/mikrotik')->with('danger', 'No existe un registro con ese id');
    }

    public function grafica($id){
        $this->getAllPermissions(Auth::user()->id);
        $mikrotik = Mikrotik::where('id', $id)->where('empresa', Auth::user()->empresa)->first();
        if ($mikrotik) {
            view()->share(['title' => 'Gráfica de Consumo', 'icon' =>'fas fa-chart-area']);
            $segmentos = Segmento::where('mikrotik', $mikrotik->id)->get();
            return view('mikrotik.grafica')->with(compact('mikrotik', 'segmentos'));
        }
        return redirect('empresa/mikrotik')->with('danger', 'No existe un registro con ese id');
    }

    public function graficajson($id, $interfaz){
        $this->getAllPermissions(Auth::user()->id);
        $mikrotik = Mikrotik::where('id', $id)->where('empresa', Auth::user()->empresa)->first();

        $API = new RouterosAPI();
        $API->port = $mikrotik->puerto_api;
        //$API->debug = true;

        if ($API->connect($mikrotik->ip,$mikrotik->usuario,$mikrotik->clave)) {
            $rows = array(); $rows2 = array(); $Type=0; $Interface='ether1';
            if ($Type==0) {  // Interfaces
                $API->write("/interface/monitor-traffic",false);
                $API->write("=interface=".$interfaz,false);
                $API->write("=once=",true);
                $READ = $API->read(false);
                $ARRAY = $API->parseResponse($READ);
                if(count($ARRAY)>0){
                    $rx = ($ARRAY[0]["rx-bits-per-second"]);
                    $tx = ($ARRAY[0]["tx-bits-per-second"]);
					$rows['name'] = 'Tx';
					$rows['data'][] = $tx;
					$rows2['name'] = 'Rx';
					$rows2['data'][] = $rx;
				}else{
					echo $ARRAY['!trap'][0]['message'];
				}
			}else if($Type==1){ //  Queues
			    $API->write("/queue/simple/print",false);
			    $API->write("=stats",false);
			    $API->write("?name=".$contrato->servicio,true);
			    $READ = $API->read(false);
			    $ARRAY = $API->parseResponse($READ);
			    if(count($ARRAY)>0){
					$rx = explode("/",$ARRAY[0]["rate"])[0];
					$tx = explode("/",$ARRAY[0]["rate"])[1];
					$rows['name'] = 'Tx';
					$rows['data'][] = $tx;
					$rows2['name'] = 'Rx';
					$rows2['data'][] = $rx;
				}else{
					echo $ARRAY['!trap'][0]['message'];
				}
			}

			$ConnectedFlag = true;

			if ($ConnectedFlag) {
			    $result = array();array_push($result,$rows);
			    array_push($result,$rows2);
			    echo json_encode($result, JSON_NUMERIC_CHECK);
			}
			$API->disconnect();
        }
    }

    public function ips_autorizadas($id){
        $this->getAllPermissions(Auth::user()->id);
        $mikrotik = Mikrotik::where('id', $id)->where('empresa', Auth::user()->empresa)->first();
        if ($mikrotik) {
            $contratos = Contrato::where('server_configuration_id', $mikrotik->id)->where('ip_autorizada', 0)->where('status', 1)->where('state', 'enabled')->get();
            view()->share(['title' => "IP's Autorizadas", 'icon' =>'fas fa-project-diagram', 'middel' => true]);
            return view('mikrotik.ips-autorizadas')->with(compact('contratos', 'mikrotik'));
        }
        return redirect('empresa/mikrotik')->with('danger', 'No existe un registro con ese id');
    }

    public function autorizar_ips($contratos){
        $this->getAllPermissions(Auth::user()->id);

        $succ = 0; $fail = 0;

        $contratos = explode(",", $contratos);

        for ($i=0; $i < count($contratos) ; $i++) {
            $contrato=Contrato::find($contratos[$i]);

            if ($contrato) {
                $mikrotik = Mikrotik::find($contrato->server_configuration_id);

                $API = new RouterosAPI();
                $API->port = $mikrotik->puerto_api;

                if ($API->connect($mikrotik->ip,$mikrotik->usuario,$mikrotik->clave)) {
                    if($mikrotik->regla_ips_autorizadas == 0){
                        $API->comm("/interface/list/add\n=name=LAN_NETWORK_SOFT");
                        $API->comm("/interface/list/add\n=interface=$mikrotik->interfaz_lan\n=list=LAN_NETWORK_SOFT");
                        $API->comm("/ip/firewall/filter/add\n=chain=forward\n=src-address-list=ips_autorizadas\n=action=accept\n=comment=IPS-AUTORIZADAS-NETWORK");
                        $API->comm("/ip/firewall/filter/add\n=chain=forward\n=src-address-list=!ips_autorizadas\n=action=drop\n=comment=IPS-NO-AUTORIZADAS-NETWORK");
                        $mikrotik->regla_ips_autorizadas = 1;
                        $mikrotik->save();
                    }

                    $API->write('/ip/firewall/address-list/print', TRUE);
                    $ARRAYS = $API->read();

                    $API->write('/ip/firewall/address-list/print', false);
                    $API->write('?address='.$contrato->ip, false);
                    $API->write("?list=ips_autorizadas",false);
                    $API->write('=.proplist=.id');
                    $ARRAYS = $API->read();

                    if(count($ARRAYS)>0){
                        $contrato->ip_autorizada = 1;
                        $contrato->save();
                        $succ++;
                    }else{
                        $API->comm("/ip/firewall/address-list/add", array(
                            "address" => $contrato->ip,
                            "comment" => $contrato->servicio,
                            "list" => 'ips_autorizadas'
                            )
                        );
                        $contrato->ip_autorizada = 1;
                        $contrato->save();
                        $succ++;
                    }
                    $API->disconnect();
                }else{
                    $fail++;
                }
            } else {
                $fail++;
            }
        }

        return response()->json([
            'success'   => true,
            'fallidos'  => $fail,
            'correctos' => $succ
        ]);
    }

    public function state_lote($mikrotiks, $state){
        $this->getAllPermissions(Auth::user()->id);

        $succ = 0; $fail = 0;

        $mikrotiks = explode(",", $mikrotiks);

        for ($i=0; $i < count($mikrotiks) ; $i++) {
            $mikrotik = Mikrotik::find($mikrotiks[$i]);

            if($mikrotik){
                if($state == 'off'){
                    $mikrotik->status = 0;
                    $succ++;
                }elseif($state == 'on'){
                    $API           = new RouterosAPI();
                    $API->port     = $mikrotik->puerto_api;
                    $API->attempts = 1;

                    if ($API->connect($mikrotik->ip,$mikrotik->usuario,$mikrotik->clave)) {
                        $API->disconnect();
                        $mikrotik->status = 1;
                        $succ++;
                    } else {
                        $fail++;
                    }
                }
                $mikrotik->save();
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

    public function destroy_lote($mikrotiks){
        $this->getAllPermissions(Auth::user()->id);

        $succ = 0; $fail = 0;

        $mikrotiks = explode(",", $mikrotiks);

        for ($i=0; $i < count($mikrotiks) ; $i++) {
            $mikrotik = Mikrotik::find($mikrotiks[$i]);
            if ($mikrotik->uso()==0) {
                $mikrotik->delete();
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

    public function arp($id){
        $this->getAllPermissions(Auth::user()->id);
        $mikrotik = Mikrotik::where('id', $id)->where('empresa', Auth::user()->empresa)->first();
        if ($mikrotik->status == 1) {
            $API = new RouterosAPI();

            $API->port = $mikrotik->puerto_api;

            if ($API->connect($mikrotik->ip,$mikrotik->usuario,$mikrotik->clave)) {
                $API->write('/ip/arp/print', true);
                $READ = $API->read(false);
                $arrays = $API->parseResponse($READ);
                $API->disconnect();
                view()->share(['title' => "Listado ARP: ".$mikrotik->nombre, 'minus_izq' => true]);
                return view('mikrotik.arp')->with(compact('arrays', 'mikrotik'));
            } else {
                return redirect('empresa/mikrotik')->with('danger', 'La mikrotik '.$mikrotik->nombre.' se encuentra desconectada')->with('mikrotik_id', $mikrotik->id);
            }
        }else{
            return redirect('empresa/mikrotik')->with('danger', 'La mikrotik '.$mikrotik->nombre.' se encuentra desconectada')->with('mikrotik_id', $mikrotik->id);
        }
        return redirect('empresa/mikrotik')->with('danger', 'No existe un registro con ese id');
    }
}
