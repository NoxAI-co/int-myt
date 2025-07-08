<?php

namespace App\Http\Controllers;

use App\Empresa;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OltController extends Controller
{
    public function unConfiguredOnus_view(Request $request)
    {

        if (Auth::user()) {
            $this->getAllPermissions(Auth::user()->id);
        } else {
            return redirect()->back()->with('error', 'No hay un usuario autenticado.');
        }

        view()->share(['title' => 'Olt - Onu Unconfigured', 'icon' => '', 'seccion' => '']);

        // ****** Get olts ****** //
        $olts = $this->getOlts();
        if (!isset($request->olt)) {
            $olt_default = null;
        } else {
            $olt_default = $request->olt;
        }
        if (isset($olts['response'])) {
            $olts = $olts['response'];
            if ($olt_default == null) {
                $olt_default = $olts[0]['id'];
            }
        } else {
            $olts = [];
        }

        if ($olt_default != null) {
            $vlan = $this->get_VLAN($olt_default);
            if (isset($vlan['response'])) {
                $vlan = $vlan['response'];
                // Usamos usort para ordenar el array según la clave 'vlan'
                usort($vlan, function ($a, $b) {
                    return (int)$a['vlan'] - (int)$b['vlan'];
                });
            } else {
                $vlan = [];
            }
        } else {
            $vlan = [];
        }
        // ****** Get olts ****** //

        // ****** Get onus by olt ****** //
        $response = $this->unconfiguredOnusOlt($olt_default);
        if (isset($response['response'])) {
            $onus = $response['response'];
        } else {
            $onus = [];
        }
        // ****** Get onus by olt ****** //
        return view('olt.unconfigured', compact('onus', 'olts', 'olt_default'));
    }

    public static function unconfiguredOnus()
    {
        $empresa = Empresa::Find(Auth::user()->empresa);
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $empresa->adminOLT . '/api/onu/unconfigured_onus',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => array(
                'X-Token: ' . $empresa->smartOLT
            ),
        ));

        $response = curl_exec($curl);
        curl_close($curl);
        $response = json_decode($response, true);

        return $response;
    }

    public static function unconfiguredOnusOlt($olt)
    {
        $empresa = Empresa::Find(Auth::user()->empresa);
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $empresa->adminOLT . '/api/onu/unconfigured_onus_for_olt/' . $olt,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => array(
                'X-Token: ' . $empresa->smartOLT
            ),
        ));

        $response = curl_exec($curl);
        curl_close($curl);
        $response = json_decode($response, true);

        return $response;
    }

    public static function onuTypes()
    {
        $empresa = Empresa::Find(Auth::user()->empresa);

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $empresa->adminOLT . '/api/system/get_onu_types',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => array(
                'X-Token: ' . $empresa->smartOLT
            ),
        ));

        $response = json_decode(curl_exec($curl), true);
        curl_close($curl);

        return $response;
    }

    public static function getOlts()
    {
        $empresa = Empresa::Find(Auth::user()->empresa);

        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $empresa->adminOLT . '/api/system/get_olts',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => array(
                'X-Token: ' . $empresa->smartOLT
            ),
        ));

        $response = json_decode(curl_exec($curl), true);
        curl_close($curl);

        return $response;
    }

    public static function get_VLAN($olt)
    {
        $curl = curl_init();
        $empresa = Empresa::Find(Auth::user()->empresa);

        curl_setopt_array($curl, array(
            CURLOPT_URL => $empresa->adminOLT . '/api/olt/get_vlans/' . $olt,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => array(
                'X-Token: ' . $empresa->smartOLT
            ),
        ));

        $response = json_decode(curl_exec($curl), true);

        curl_close($curl);
        return $response;
    }

    public function getZones()
    {
        $curl = curl_init();
        $empresa = Empresa::Find(Auth::user()->empresa);

        curl_setopt_array($curl, array(
            CURLOPT_URL => $empresa->adminOLT . '/api/system/get_zones',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => array(
                'X-Token: ' . $empresa->smartOLT
            ),
        ));

        $response = json_decode(curl_exec($curl), true);

        curl_close($curl);
        return $response;
    }

    public function ODBlist($zone)
    {

        $curl = curl_init();
        $empresa = Empresa::Find(Auth::user()->empresa);

        curl_setopt_array($curl, array(
            CURLOPT_URL => $empresa->adminOLT . '/api/system/get_odbs/' . $zone,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => array(
                'X-Token: ' . $empresa->smartOLT
            ),
        ));


        $response = json_decode(curl_exec($curl), true);
        curl_close($curl);
        return $response;
    }

    public function getSpeedProfiles()
    {
        $curl = curl_init();
        $empresa = Empresa::Find(Auth::user()->empresa);

        curl_setopt_array($curl, array(
            CURLOPT_URL => $empresa->adminOLT . '/api/system/get_speed_profiles',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => array(
                'X-Token: ' . $empresa->smartOLT
            ),
        ));

        $response = json_decode(curl_exec($curl), true);

        curl_close($curl);
        return $response;
    }


    public function formAuthorizeOnu(Request $request)
    {

        $this->getAllPermissions(Auth::user()->id);
        view()->share(['title' => 'Olt - Formulario Authorizacion Onu', 'icon' => '', 'seccion' => '']);

        $onu_types = $this->onuTypes();

        if (isset($onu_types['response'])) {
            $onu_types = $onu_types['response'];
        } else {
            $onu_types = [];
        }

        $olts = $this->getOlts();
        $olt_default = $request->olt_id;
        if (isset($olts['response'])) {
            $olts = $olts['response'];
        } else {
            $olts = [];
        }

        if ($olt_default != null) {
            $vlan = $this->get_VLAN($olt_default);
            if (isset($vlan['response'])) {
                $vlan = $vlan['response'];

                // Usamos usort para ordenar el array según la clave 'vlan'
                usort($vlan, function ($a, $b) {
                    return (int)$a['vlan'] - (int)$b['vlan'];
                });
            } else {
                $vlan = [];
            }
        } else {
            $vlan = [];
        }

        $zones = $this->getZones();
        $default_zone = 0;

        if (isset($zones['response'])) {
            $zones = $zones['response'];
            $default_zone = $zones[0]['id'];
        } else {
            $zones = [];
        }

        if ($default_zone != 0) {
            $odbList = $this->ODBlist($default_zone);
            if (isset($odbList['response'])) {
                $odbList = $odbList['response'];
            } else {
                $odbList = [];
            }
        } else {
            $odbList = [];
        }

        $speedProfiles = $this->getSpeedProfiles();

        if (isset($speedProfiles['response'])) {
            $speedProfiles = $speedProfiles['response'];
        } else {
            $speedProfiles = [];
        }

        return view('olt.form-authorized-onu', compact(
            'request',
            'onu_types',
            'olts',
            'vlan',
            'zones',
            'olt_default',
            'default_zone',
            'odbList',
            'speedProfiles'
        ));
    }

    public function authorizedOnus(Request $request)
    {

        $empresa = Empresa::Find(Auth::user()->empresa);

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $empresa->adminOLT . '/api/onu/authorize_onu',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => array(
                'olt_id' => $request->olt_id,
                'pon_type' => $request->pon_type,
                'board' => $request->board,
                'port' => $request->port,
                'sn' => $request->sn,
                'onu_type' => $request->onu_type,
                'custom_profile' => '',
                'onu_mode' => $request->onu_mode,
                'cvlan' => '',
                'svlan' => '',
                'tag_transform_mode' => '',
                'use_other_all_tls_vlan' => '',
                'vlan' => $request->user_vlan_id,
                'zone' => $request->zone,
                'odb' => $request->odb_splitter,
                'name' => $request->name,
                'address_or_comment' => $request->address_comment,
                'onu_external_id' => $request->sn,
                'upload_speed_profile_name' => $request->upload_speed,
                'download_speed_profile_name' => $request->download_speed
            ),
            CURLOPT_HTTPHEADER => array(
                'X-Token: ' . $empresa->smartOLT
            ),
        ));

        $response = curl_exec($curl);
        $response = json_decode($response, true);

        curl_close($curl);

        if ($response['status'] == 200) {
            $mensaje = "Onu autorizada con exito";
            return redirect('Olt/unconfigured-onus')->with('success', $mensaje);
        } else {
            $mensaje = "Onu no ha sido autorizada";
            return redirect('Olt/unconfigured-onus')->with('error', $mensaje);
        }
    }

    public static function moveOnuSpecified($olt_id, $board, $port, $sn)
    {
        $empresa = Empresa::Find(Auth::user()->empresa);

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $empresa->adminOLT . '/api/onu/move/' . $sn,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => array(
                'olt_id' => $olt_id,
                'board' => $board,
                'port' => $port,
            ),
            CURLOPT_HTTPHEADER => array(
                'X-Token: ' . $empresa->smartOLT
            ),
        ));

        $response = curl_exec($curl);
        $response = json_decode($response, true);

        curl_close($curl);

        return $response;
    }

    public function moveOnu(Request $request)
    {

        $response = $this->moveOnuSpecified($request->olt_id, $request->board, $request->port, $request->sn);

        if (isset($response['response']) && $response['status'] == true) {
            return response()->json([
                'status' => 200
            ]);
        } else {
            return response()->json([
                'status' => 400
            ]);
        }
    }

    public function resyncOnuConfig($sn)
    {
        $empresa = Empresa::Find(Auth::user()->empresa);

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $empresa->adminOLT . '/api/onu/resync_config/' . $sn,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => array(),
            CURLOPT_HTTPHEADER => array(
                'X-Token: ' . $empresa->smartOLT
            ),
        ));

        $response = curl_exec($curl);
        $response = json_decode($response, true);

        curl_close($curl);

        return $response;
    }

    public function getFullOnuSignal($sn)
    {
        $empresa = Empresa::Find(Auth::user()->empresa);

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $empresa->adminOLT . '/api/onu/get_onu_full_status_info/' . $sn,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_POSTFIELDS => array(),
            CURLOPT_HTTPHEADER => array(
                'X-Token: ' . $empresa->smartOLT
            ),
        ));

        $response = curl_exec($curl);
        $response = json_decode($response, true);

        curl_close($curl);

        return $response;
    }

    public function rebootOnu($sn)
    {
        $empresa = Empresa::Find(Auth::user()->empresa);

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $empresa->adminOLT . '/api/onu/reboot/' . $sn,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => array(),
            CURLOPT_HTTPHEADER => array(
                'X-Token: ' . $empresa->smartOLT
            ),
        ));

        $response = curl_exec($curl);
        $response = json_decode($response, true);

        curl_close($curl);

        return $response;
    }

    public function restoreFactory($sn)
    {
        $empresa = Empresa::Find(Auth::user()->empresa);

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $empresa->adminOLT . '/api/onu/restore_factory_defaults/' . $sn,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => array(),
            CURLOPT_HTTPHEADER => array(
                'X-Token: ' . $empresa->smartOLT
            ),
        ));

        $response = curl_exec($curl);
        $response = json_decode($response, true);

        curl_close($curl);

        return $response;
    }

    public function resyncConfig(Request $request)
    {
        $response = $this->resyncOnuConfig($request->sn);

        if (isset($response['response']) && $response['status'] == true) {
            return response()->json([
                'status' => 200
            ]);
        } else {
            return response()->json([
                'status' => 400
            ]);
        }
    }

    public function enableOnu($sn)
    {
        $empresa = Empresa::Find(Auth::user()->empresa);

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $empresa->adminOLT . '/api/onu/enable/' . $sn,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => array(),
            CURLOPT_HTTPHEADER => array(
                'X-Token: ' . $empresa->smartOLT
            ),
        ));

        $response = curl_exec($curl);
        $response = json_decode($response, true);

        curl_close($curl);

        return $response;
    }

    public function disableOnu($sn)
    {
        $empresa = Empresa::Find(Auth::user()->empresa);

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $empresa->adminOLT . '/api/onu/disable/' . $sn,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => array(),
            CURLOPT_HTTPHEADER => array(
                'X-Token: ' . $empresa->smartOLT
            ),
        ));

        $response = curl_exec($curl);
        $response = json_decode($response, true);

        curl_close($curl);

        return $response;
    }

    public function deleteOnu($sn)
    {
        $empresa = Empresa::Find(Auth::user()->empresa);

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $empresa->adminOLT . '/api/onu/delete/' . $sn,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => array(),
            CURLOPT_HTTPHEADER => array(
                'X-Token: ' . $empresa->smartOLT
            ),
        ));

        $response = curl_exec($curl);
        $response = json_decode($response, true);

        curl_close($curl);

        return $response;
    }

    public function rebootOnuResponse(Request $request)
    {
        $response = $this->rebootOnu($request->sn);

        if (isset($response['response']) && $response['status'] == true) {
            return response()->json([
                'status' => 200
            ]);
        } else {
            return response()->json([
                'status' => 400
            ]);
        }
    }

    public function restoreFactoryResponse(Request $request)
    {
        $response = $this->restoreFactory($request->sn);

        if (isset($response['response']) && $response['status'] == true) {
            return response()->json([
                'status' => 200
            ]);
        } else {
            return response()->json([
                'status' => 400
            ]);
        }
    }

    public function restoreDefaultResponse(Request $request)
    {
        $response = $this->restoreDefault($request->sn);

        if (isset($response['response']) && $response['status'] == true) {
            return response()->json([
                'status' => 200
            ]);
        } else {
            return response()->json([
                'status' => 400
            ]);
        }
    }

    public function disableOnuResponse(Request $request)
    {
        $response = $this->disableOnu($request->sn);

        if (isset($response['response']) && $response['status'] == true) {
            return response()->json([
                'status' => 200
            ]);
        } else {
            return response()->json([
                'status' => 400
            ]);
        }
    }

    public function enableOnuResponse(Request $request)
    {
        $response = $this->enableOnu($request->sn);

        if (isset($response['response']) && $response['status'] == true) {
            return response()->json([
                'status' => 200
            ]);
        } else {
            return response()->json([
                'status' => 400
            ]);
        }
    }

    public function deleteOnuResponse(Request $request)
    {
        $response = $this->deleteOnu($request->sn);

        if (isset($response['response']) && $response['status'] == true) {
            return response()->json([
                'status' => 200
            ]);
        } else {
            return response()->json([
                'status' => 400
            ]);
        }
    }

    public function onu_type_image($onu_type_id)
    {
        $empresa = Empresa::Find(Auth::user()->empresa);

        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $empresa->adminOLT . '/api/system/get_onu_type_image/' . $onu_type_id,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_POSTFIELDS => array(),
            CURLOPT_HTTPHEADER => array(
                'X-Token: ' . $empresa->smartOLT
            ),
        ));

        $response = curl_exec($curl);
        curl_close($curl);

        return $response;
    }

    public function getOnuDetailsBySn($sn)
    {
        $empresa = Empresa::Find(Auth::user()->empresa);

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $empresa->adminOLT . '/api/onu/get_onus_details_by_sn/' . $sn,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_POSTFIELDS => array(),
            CURLOPT_HTTPHEADER => array(
                'X-Token: ' . $empresa->smartOLT
            ),
        ));

        $response = curl_exec($curl);
        $response = json_decode($response, true);

        curl_close($curl);

        return $response;
    }

    public function onu_traffic_image($sn)
    {
        $empresa = Empresa::Find(Auth::user()->empresa);
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $empresa->adminOLT . '/api/onu/get_onu_traffic_graph/' . $sn . '/daily',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_POSTFIELDS => array(),
            CURLOPT_HTTPHEADER => array(
                'X-Token: ' . $empresa->smartOLT
            ),
        ));

        $response = curl_exec($curl);
        curl_close($curl);

        return $response;
    }

    public function onu_signal_image($sn)
    {
        $empresa = Empresa::Find(Auth::user()->empresa);

        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $empresa->adminOLT . '/api/onu/get_onu_signal_graph/' . $sn . '/daily',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_POSTFIELDS => array(),
            CURLOPT_HTTPHEADER => array(
                'X-Token: ' . $empresa->smartOLT
            ),
        ));

        $response = curl_exec($curl);
        curl_close($curl);

        return $response;
    }


    public function onu_signal($sn)
    {
        $empresa = Empresa::Find(Auth::user()->empresa);

        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $empresa->adminOLT . '/api/onu/get_onu_signal/' . $sn,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_POSTFIELDS => array(),
            CURLOPT_HTTPHEADER => array(
                'X-Token: ' . $empresa->smartOLT
            ),
        ));

        $response = curl_exec($curl);
        $response = json_decode($response, true);
        curl_close($curl);

        return $response;
    }


    public function runningConfig($sn)
    {
        $empresa = Empresa::Find(Auth::user()->empresa);

        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $empresa->adminOLT . '/api/onu/get_running_config/' . $sn,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_POSTFIELDS => array(),
            CURLOPT_HTTPHEADER => array(
                'X-Token: ' . $empresa->smartOLT
            ),
        ));

        $response = curl_exec($curl);
        $response = json_decode($response, true);
        curl_close($curl);

        return $response;
    }

    public function onu_status($sn)
    {
        $empresa = Empresa::Find(Auth::user()->empresa);

        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $empresa->adminOLT . '/api/onu/get_onu_status/' . $sn,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_POSTFIELDS => array(),
            CURLOPT_HTTPHEADER => array(
                'X-Token: ' . $empresa->smartOLT
            ),
        ));

        $response = curl_exec($curl);
        $response = json_decode($response, true);
        curl_close($curl);

        return $response;
    }

    public function viewOnu(Request $request)
    {

        $sn = $request->sn;
        $this->getAllPermissions(Auth::user()->id);
        view()->share(['title' => $sn, 'icon' => '', 'seccion' => '']);

        if ($sn) {
            $details = $this->getOnuDetailsBySn($sn);
            if ($details['status'] != true || !isset($details['onus'][0])) {
                return redirect('Olt/unconfigured-onus')->with('error', 'Error al mirar la informacion de la onu');
            }
        } else {
            return redirect('Olt/unconfigured-onus')->with('error', 'No hay una sn seleccionada');
        }

        $details = $details['onus'][0];

        $image_onu_type = null;
        if (isset($details['onu_type_id'])) {
            $image_onu_type = $this->onu_type_image($details['onu_type_id']);
            $imagenBase64 = base64_encode($image_onu_type);
            $image_onu_type = 'data:image/png;base64,' . $imagenBase64;
        }

        $onu_traffic_graph = null;
        if (isset($details['onu_type_id'])) {
            $onu_traffic_graph = $this->onu_traffic_image($details['sn']);
            $imagenBase64 = base64_encode($onu_traffic_graph);
            $onu_traffic_graph = 'data:image/png;base64,' . $imagenBase64;
        }
        $onu_signal_graph = null;
        if (isset($details['onu_type_id'])) {
            $onu_signal_graph = $this->onu_signal_image($details['sn']);
            $imagenBase64 = base64_encode($onu_signal_graph);
            $onu_signal_graph = 'data:image/png;base64,' . $imagenBase64;
        }

        // $fullStatusInfo = $this->getFullOnuSignal($sn);
        $onlySignal = $this->onu_signal($sn);

        $onuStatus = $this->onu_status($sn);
        $diferenciaHoras = "-";

        if (isset($onuStatus['last_status_change'])) {
            if (!empty($onuStatus['last_status_change']) &&  $onuStatus['last_status_change'] !== "-") {
                $lastStatusDate = Carbon::createFromFormat('Y-m-d H:i:s', $onuStatus['last_status_change']);
                $now = Carbon::now();
                $diferenciaHoras = $now->diffInHours($lastStatusDate);

                // Calcular los días completos y las horas restantes
                $dias = floor($diferenciaHoras / 24); // Días completos
                $horas = $diferenciaHoras % 24; // Horas restantes

                // Crear el mensaje
                if ($dias > 0) {
                    $diferenciaHoras = "Hace $dias días y $horas horas";
                } else {
                    $diferenciaHoras = "Hace $horas horas";
                }
            } else {
                // Manejar el caso cuando 'last_status_change' no está presente o es null
                $lastStatusDate = null;
            }
        }

        $ethernetPorts = isset($details['ethernet_ports']) ? $details['ethernet_ports'] : [];
        return view('olt.view-onu', compact(
            'details',
            'image_onu_type',
            'ethernetPorts',
            'onu_traffic_graph',
            'onu_signal_graph',
            'onlySignal',
            'diferenciaHoras',
            'onuStatus'
        ));
    }

    public function update_vlan(Request $request){

        $empresa = Empresa::Find(Auth::user()->empresa);

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $empresa->adminOLT . '/api/onu/update_attached_vlans/' . $request->sn,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => array(
                'add_vlans'    => !empty($request->add_vlans) ? implode(',', $request->add_vlans) : '',
                'remove_vlans' => !empty($request->remove_vlans) ? implode(',', $request->remove_vlans) : '',
            ),
            CURLOPT_HTTPHEADER => array(
                'X-Token: ' . $empresa->smartOLT
            ),
        ));

        $response = curl_exec($curl);
        $response = json_decode($response, true);

        curl_close($curl);

        return response()->json($response);
    }

    public function update_ethernet_port(Request $request){

        if(isset($request->status) && $request->status == "enabled"){
            switch ($request->mode) {
                case 'LAN':
                    $response = $this->update_ethernet_LAN($request->sn, $request->ethernet_port, $request->dhcp);
                    break;
    
                case 'Access':
                    $response = $this->update_ethernet_Access($request->sn, $request->ethernet_port, $request->dhcp, $request->vlan);
                    break;
    
                case 'Hybrid':
                    $response = $this->update_ethernet_Hybrid($request->sn, $request->ethernet_port, $request->dhcp, $request->vlan, implode(',', $request->allowed_vlans));
                    break;
                
                case 'Trunk':
                    $response = $this->update_ethernet_Trunk($request->sn, $request->ethernet_port, $request->vlan, implode(',', $request->allowed_vlans));
                    break;
    
                case 'Trunk':
                    $response = $this->update_ethernet_Transparent($request->sn, $request->ethernet_port, $request->dhcp);
                    break;
                
                default:
                    break;
             }

             return response()->json($response);
        }else{
            $response = [
                'status' => false,
                'message' => 'El puerto ethernet no se puede deshabilitar'
            ];
            return response()->json($response);
        }


    }

    public static function update_ethernet_LAN($sn, $ethernet_port,$dhcp){

        $empresa = Empresa::Find(Auth::user()->empresa);
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $empresa->adminOLT . '/api/onu/set_ethernet_port_lan/' . $sn,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => array(
                'ethernet_port' => $ethernet_port,
                'dhcp' => $dhcp,
            ),
            CURLOPT_HTTPHEADER => array(
                'X-Token: ' . $empresa->smartOLT
            ),
        ));

        $response = curl_exec($curl);
        $response = json_decode($response, true);

        curl_close($curl);

        return $response;

    }

    public static function update_ethernet_Access($sn, $ethernet_port,$dhcp, $vlan){
        
        $empresa = Empresa::Find(Auth::user()->empresa);
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $empresa->adminOLT . '/api/onu/set_ethernet_port_access/' . $sn,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => array(
                'ethernet_port' => $ethernet_port,
                'dhcp' => $dhcp,
                'vlan' => $vlan
            ),
            CURLOPT_HTTPHEADER => array(
                'X-Token: ' . $empresa->smartOLT
            ),
        ));

        $response = curl_exec($curl);
        $response = json_decode($response, true);

        curl_close($curl);

        return $response;

    }

    public static function update_ethernet_Hybrid($sn, $ethernet_port,$dhcp, $vlan, $allowed_vlans){

        $empresa = Empresa::Find(Auth::user()->empresa);
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $empresa->adminOLT . '/api/onu/set_ethernet_port_hybrid/' . $sn,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => array(
                'ethernet_port' => $ethernet_port,
                'dhcp' => $dhcp,
                'vlan' => $vlan,
                'allowed_vlans' => $allowed_vlans
            ),
            CURLOPT_HTTPHEADER => array(
                'X-Token: ' . $empresa->smartOLT
            ),
        ));

        $response = curl_exec($curl);
        $response = json_decode($response, true);

        curl_close($curl);

        return $response;

    }

    public static function update_ethernet_Trunk($sn, $ethernet_port, $vlan, $allowed_vlans){

        $empresa = Empresa::Find(Auth::user()->empresa);
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $empresa->adminOLT . '/api/onu/set_ethernet_port_trunk/' . $sn,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => array(
                'ethernet_port' => $ethernet_port,
                'vlan' => $vlan,
                'allowed_vlans' => $allowed_vlans
            ),
            CURLOPT_HTTPHEADER => array(
                'X-Token: ' . $empresa->smartOLT
            ),
        ));

        $response = curl_exec($curl);
        $response = json_decode($response, true);

        curl_close($curl);

        return $response;

    }

    public static function update_ethernet_Transparent($sn, $ethernet_port, $dhcp){

        $empresa = Empresa::Find(Auth::user()->empresa);
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $empresa->adminOLT . '/api/onu/set_ethernet_port_trunk/' . $sn,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => array(
                'ethernet_port' => $ethernet_port,
                'dhcp' => $dhcp
            ),
            CURLOPT_HTTPHEADER => array(
                'X-Token: ' . $empresa->smartOLT
            ),
        ));

        $response = curl_exec($curl);
        $response = json_decode($response, true);

        curl_close($curl);

        return $response;

    }

    public function getPorts($olt_id){
        $empresa = Empresa::Find(Auth::user()->empresa);

        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $empresa->adminOLT . '/api/system/get_olt_pon_ports_details/' . $olt_id,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => array(
                'X-Token: ' . $empresa->smartOLT
            ),
        ));

        $response = json_decode(curl_exec($curl), true);
        curl_close($curl);

        return $response;
    }

    public function getBoards($olt_id){
        $empresa = Empresa::Find(Auth::user()->empresa);

        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $empresa->adminOLT . '/api/system/get_olt_cards_details/' . $olt_id,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => array(
                'X-Token: ' . $empresa->smartOLT
            ),
        ));

        $response = json_decode(curl_exec($curl), true);
        curl_close($curl);

        return $response;
    }


    public function getModalMoveOnu(Request $request){
        
        try {
            $olt = $this->getOlts();
            $board = $this->getBoards($request->olt_id);
            $ports = [];

            $board = array_filter($board['response'], function ($item) {
                return isset($item['type']) && $item['type'] === 'GTGH';
            });
            
            return response()->json([
                'status' => true,
                'olts' => $olt,
                'boards' => array_values($board),
                'ports' => $ports,
                'sn' => $request->sn
            ]);

        } catch (\Exception $e) {

            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    public function updateMoveOnuModal(Request $request){
        
        $response = $this->moveOnuSpecified($request->olt_id, $request->board, $request->port, $request->sn);

        if (isset($response['response']) && $response['status'] == true) {
            return response()->json([
                'status' => 200
            ]);
        } else {
            return response()->json([
                'status' => 400
            ]);
        }
    }
}
