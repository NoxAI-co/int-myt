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
                    <h5>ONU Information</h5>
                    <ul class="list-unstyled">
                        <li><strong>OLT:</strong> <a href="#">OLT YONDO</a></li>
                        <li><strong>Board:</strong> 1</li>
                        <li><strong>Port:</strong> 13</li>
                        <li><strong>ONU:</strong> gpon-onu_0/1/13:9</li>
                        <li><strong>SN:</strong> <a href="#">HWTCD2D507E8</a></li>
                        <li><strong>ONU Type:</strong> <a href="#">ONU-type-eth-4-pots-2-catv-0</a></li>
                        <li><strong>Zone:</strong> <a href="#">Zone 1</a></li>
                        <li><strong>ODB (Splitter):</strong> <em>None</em></li>
                        <li><strong>Name:</strong> JOSE GABRIEL LERMA FLOREZ</li>
                        <li><strong>Address or Comment:</strong> <em>None</em></li>
                        <li><strong>Contact:</strong> <em>None</em></li>
                        <li><strong>Authorization Date:</strong> <a href="#">History</a></li>
                        <li><strong>ONU External ID:</strong> <a href="#">HWTCD2D507E8</a></li>
                    </ul>
                </div>

                <!-- Status and Signals -->
                <div class="col-md-6">
                    <img src="{{ asset('images/wifi-modem.png') }}" alt="wifi modem" class="img-fluid mb-4">
                    <h5>ONU Status</h5>
                    <ul class="list-unstyled">
                        <li><strong>Status:</strong> Power fail <i>(1 week ago)</i></li>
                        <li><strong>ONU/OLT Rx Signal:</strong> -</li>
                        <li><strong>Attached VLANs:</strong> <a href="#">200</a></li>
                        <li><strong>ONU Mode:</strong> Routing - WAN vlan: 200</li>
                        <li><strong>TR069:</strong> Inactive</li>
                        <li><strong>Mgmt IP:</strong> Inactive</li>
                        <li><strong>WAN Setup Mode:</strong> <a href="#">Setup via ONU webpage</a></li>
                    </ul>
                </div>
            </div>

            <!-- Buttons -->
            <div class="mt-3">
                <button class="btn btn-primary">Get Status</button>
                <button class="btn btn-primary">Show Running-Config</button>
                <button class="btn btn-primary">SW Info</button>
                <button class="btn btn-success">LIVE!</button>
            </div>

            <!-- Traffic and Signal Charts -->
            <div class="mt-4">
                <h5>Traffic/Signal</h5>
                <div class="row">
                    <div class="col-md-6">
                        <h6>Daily Traffic</h6>
                        <img src="path/to/traffic_daily.png" alt="Daily Traffic Graph" class="img-fluid">
                    </div>
                    <div class="col-md-6">
                        <h6>Weekly Signal</h6>
                        <img src="path/to/signal_weekly.png" alt="Weekly Signal Graph" class="img-fluid">
                    </div>
                </div>
            </div>

            <h5 class="mt-4">Speed Profiles</h5>
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
        
            <h5 class="mt-4">Ethernet Ports</h5>
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

            <h5>VoIP Service: <span style="color: #0073ea;">Disabled</span></h5>
            <h5>IPTV: <span style="color: #0073ea;">Inactive</span></h5>
            <h5>CATV: <em>Not supported by ONU-Type.</em></h5>
        
            <div class="buttons text-left">
                <button class="btn-reboot">Reboot</button>
                <button class="btn-resync">Resync config</button>
                <button class="btn-restore">Restore defaults</button>
                <button class="btn-disable">Disable ONU</button>
                <button class="btn-delete">Delete</button>
            </div>
    
        </div>
    </div>
</div>

@endsection

@section('scripts')

@endsection