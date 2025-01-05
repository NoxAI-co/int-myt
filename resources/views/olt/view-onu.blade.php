@extends('layouts.app')
<style>
    body {
        font-family: Arial, sans-serif;
        margin: 20px;
    }
    table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 20px;
    }
    table, th, td {
        border: 1px solid #ddd;
    }
    th, td {
        padding: 10px;
        text-align: center;
    }
    th {
        background-color: #f2f2f2;
    }
    a {
        color: #0073ea;
        text-decoration: none;
    }
    a:hover {
        text-decoration: underline;
    }
    .buttons {
        margin-top: 20px;
        text-align: center;
    }
    .buttons button {
        margin: 5px;
        padding: 10px 20px;
        border: none;
        cursor: pointer;
        /* font-size: 14px; */
    }
    .btn-reboot, .btn-resync, .btn-restore, .btn-disable {
        background-color: #ffcc00;
        color: #333;
    }
    .btn-delete {
        background-color: #e74c3c;
        color: #fff;
    }

    .font-pair, .font-pair ul, .font-pair table{
        font-size:13px;
    }

    .xp7jhwk{
        padding-bottom: 2px !important;
    }

    /* Estilos para los ul li */
    .list-unstyled ul {
            list-style-type: none; /* Elimina los puntos de la lista */
            padding: 0;
            margin: 0;
        }

        .list-unstyled li {
            display: flex; /* Organiza cada elemento en fila */
            justify-content: space-between; /* Separa título y valor */
            padding: 4px 0; /* Espaciado entre filas */
            border-bottom: 1px solid #ddd; /* Línea divisoria opcional */
        }

        .list-unstyled li span.title {
            font-weight: bold; /* Hace los títulos en negrita */
            color: #333;
            flex: 0 0 30%; /* Asigna un 30% del espacio al título */
            text-align: right;
        }

        .list-unstyled li span.title-2 {
            font-weight: bold; /* Hace los títulos en negrita */
            color: #333;
            flex: 0 0 15%; /* Asigna un 30% del espacio al título */
            text-align: right;
        }
        

        .list-unstyled li span.value {
            color: #007bff; /* Color azul similar al ejemplo */
            flex: 1; /* El valor ocupa el espacio restante */
            margin-left: 10px;
        }

</style>
@section('content')
<div class="mt-0 font-pair">
    <div class="card">
        <div class="card-header bg-primary text-white">
            ONU Status Overview
        </div>
        
        <div class="card-body container">
            <div class="row">
                <!-- OLT Details -->
                <div class="col-md-6">
                    <ul class="list-unstyled">
                        <li><span class="title">OLT:</span> 
                            <span class="value"><a href="#">{{ $details['olt_name'] }}</a></span>
                        </li>
                        <li><span class="title">Board:</span> 
                            <span class="value">{{ $details['board'] }}</span> 
                        </li>
                        <li><span class="title">Port:</span> 
                            <span class="value">{{ $details['port'] }}</span> 
                        </li>
                        <li><span class="title">ONU:</span> 
                            <span class="value">{{ $details['pon_type'] . "/" . $details['board'] . "/" . 
                            $details['port'] . ":" . $details['onu'] }}</span>
                        </li>
                        <li><span class="title">SN:</span> 
                            <span class="value"><a href="#">{{ $details['sn'] }}</a></span>
                        </li>
                        <li><span class="title">ONU Type:</span> 
                            <span class="value"><a href="#">{{ $details['onu_type_name'] }}</a></span>
                        </li>
                        <li><span class="title">Zone:</span> 
                            <span class="value"><a href="#">{{ $details['zone_name'] }}</a></span>
                        </li>
                        <li><span class="title">ODB (Splitter):</span>
                            <span class="value">None</span> 
                        </li>
                        <li><span class="title">Name:</span> 
                            <span class="value">{{ $details['name'] }}</span>
                        </li>
                        <li><span class="title">Address or Comment:</span>
                            <span class="value">{{ $details['address'] }}</span> 
                        </li>
                        <li><span class="title">Contact:</span> 
                            <span class="value">{{ $details['contact'] }}</span>
                        </li>
                        <li><span class="title">Authorization Date:</span> 
                            <span class="value"><a href="#">History</a></span>
                        </li>
                        <li><span class="title">ONU External ID:</span> 
                            <span class="value"><a href="#">{{ $details['unique_external_id'] }}</a></span>
                        </li>
                    </ul>
                </div>

                <!-- Status and Signals -->
                <div class="col-md-6">
                    <img src="{{ $image_onu_type }}" alt="wifi modem" class="img-fluid mb-4">
                    <ul class="list-unstyled">
                        <li><span class="title">Status:</span> 
                            <span class="value">Power fail <i>(1 week ago)</i></span> 
                        </li>
                        <li><span class="title">ONU/OLT Rx Signal:</span> 
                            <span class="value">-</span>
                        </li>
                        <li><span class="title">Attached VLANs:</span> 
                            <span class="value"><a href="#">{{ $details['vlan'] }}</a></span>
                        </li>
                        <li><span class="title">ONU Mode:</span> <span class="value">{{ $details['mode'] }} - WAN vlan: {{ $details['vlan'] }}</span>
                        </li>
                        <li><span class="title">TR069:</span> 
                            <span class="value">{{ $details['tr069_profile'] }}</span>
                        </li>
                        <li><span class="title">Mgmt IP:</span> 
                            <span class="value">{{ $details['mgmt_ip_mode'] }}</span>
                        </li>
                        <li><span class="title">WAN Setup Mode:</span> 
                            <span class="value"><a href="#">Setup via ONU webpage</a></span> 
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Buttons -->
            <div class="row">
                <div class="col-md-12">
                    <ul class="list-unstyled">
                        <li>
                          <span class="title-2">Status:</span>
                           <span class="value">
                                <button class="btn btn-primary">Get Status</button>
                                <button class="btn btn-primary">Show Running-Config</button>
                                <button class="btn btn-primary">SW Info</button>
                                <button class="btn btn-success">LIVE!</button>
                           </span>
                          
                        </li>
                    </ul>
                </div>
            </div>
            
            <!-- Traffic and Signal Charts -->
            <div class="mt-4">
                <h5>Traffic/Signal</h5>
                <div class="row">
                    <div class="col-md-6">
                        <img src="{{ $onu_traffic_graph }}" alt="Daily Traffic Graph" class="img-fluid">
                    </div>
                    <div class="col-md-6">
                        <img src="{{ $onu_signal_graph }}" alt="Weekly Signal Graph" class="img-fluid" >
                    </div>
                </div>
            </div>
            
            <div class="col-md-12">
            <ul class="list-unstyled mt-4">
                <li>                
                    <span class="title-2">Speed Profiles:</span>
                    <span class="value">
                        <table>
                            <thead>
                                <tr>
                                    <th>Service-port ID</th>
                                    <th>User-VLAN</th>
                                    <th>Download</th>
                                    <th>Upload</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>{{ 129 }}</td>
                                    <td>{{ 200 }}</td>
                                    <td>{{ "1G" }}</td>
                                    <td>{{ "1G" }}</td>
                                    <td><a href="#">+ Configure</a></td>
                                </tr>
                            </tbody>
                        </table>
                    </span>
                </li>
            </ul>
        </div>
            
           
            <div class="col-md-12">
                <ul class="list-unstyled">
                    <li>
                        <span class="title-2">Ethernet Ports:</span>
                        <span class="value">
                            <table>
                                <thead>
                                    <tr>
                                        <th>Port</th>
                                        <th>Admin State</th>
                                        <th>Mode</th>
                                        <th>DHCP</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($ethernetPorts as $port)
                                    <tr>
                                        <td>{{ $port['name'] }}</td>
                                        <td>{{ $port['adminState'] }}</td>
                                        <td>{{ $port['mode'] }}</td>
                                        <td>{{ $port['dhcp'] }}</td>
                                        <td><a href="#">+ Configure</a></td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </span>
                    </li>

                    <li>
                        <span class="title-2">VoIP Service:</span>
                        <span class="value">Disabled</span>
                    </li>
                    <li>
                        <span class="title-2">IPTV:</span>
                        <span class="value">Inactive</span>
                    </li>
                    <li>
                        <span class="title-2">CATV:</span>
                        <span class="value">Not supported by ONU-Type.</span>
                    </li>
                        {{-- <h5>VoIP Service: <span style="color: #0073ea;">Disabled</span></h5>
                        <h5>IPTV: <span style="color: #0073ea;">Inactive</span></h5>
                        <h5>CATV: <em>Not supported by ONU-Type.</em></h5> --}}

                    </li>
                </ul>
            </div>

        <div class="col-md-12">
            <ul class="list-unstyled">
                <li>
                    <span class="title-2"></span>
                    <span class="value">
                        <div class="buttons text-left">
                            <button class="btn-reboot" onclick="">Reboot</button>
                            <button class="btn-resync">Resync config</button>
                            <button class="btn-restore">Restore defaults</button>
                            <button class="btn-disable">Disable ONU</button>
                            <button class="btn-delete">Delete</button>
                        </div>
                    </span>
                </li>
            </ul>
        </div>
    
        </div>
    </div>
</div>

@endsection

@section('scripts')

@endsection