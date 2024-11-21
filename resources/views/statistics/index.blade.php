@extends('layouts.app')
@section('style')
    <style>
        .nav-tabs .nav-link {
            font-size: 1em;
        }
        .nav-tabs .nav-link.active, .nav-tabs .nav-item.show .nav-link {
            background-color: #b00606;
            color: #fff!important;
        }
        .nav-pills .nav-link.active, .nav-pills .show > .nav-link {
            color: #fff!important;
            background-color: #b00606!important;
        }
        .nav-pills .nav-link {
            font-weight: 700!important;
        }
        .nav-pills .nav-link{
            color: #b00606!important;
            background-color: #f9f9f9!important;
            margin: 2px;
            border: 1px solid #b00606;
            transition: 0.4s;
        }
        .nav-pills .nav-link:hover {
            color: #fff!important;
            background-color: #b00606!important;
        }

        .custom-card {
            background-color: #fff!important; /* Fondo blanco */
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); /* Sombra ligera */
            border-radius: 8px; /* Bordes redondeados para un mejor acabado */
            border: none; /* Eliminar borde */
        }
    </style>
@endsection
@section('content')
    @if(Session::has('success'))
        <div class="alert alert-success" >
            {{Session::get('success')}}
        </div>

        <script type="text/javascript">
            setTimeout(function(){
                $('.alert').hide();
                $('.active_table').attr('class', ' ');
            }, 5000);
        </script>
    @endif
    @if(Session::has('danger'))
        <div class="alert alert-danger" >
            {{Session::get('danger')}}
        </div>
    @endif

    <div class="row m-2">
        <div class="col-md-3">
            <div class="card custom-card"> <!-- Usamos la clase personalizada -->
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">{{ $activeClients["current"] }}</h5>
                        <i class="fas fa-users"></i>
                    </div>
                    <small class="text-muted d-block">Total clientes Activos</small>
                    <hr> <!-- Línea divisoria -->
                    <div class="d-flex justify-content-between">
                        <small class="text-muted">Mes anterior: {{ $activeClients["previous"] }}</small>
                        <div class="{{ $activeClients["percentageChange"] < 0 ? 'text-danger' : 'text-success' }}">

                            <i class="fas fa-arrow-{{ $activeClients["percentageChange"] < 0 ? 'down' : 'up' }}"></i>
                            {{ number_format(abs($activeClients["percentageChange"]), 2) }}%
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tarjeta 2: Clientes con Internet -->
        <div class="col-md-3">
            <div class="card custom-card"> <!-- Usamos la clase personalizada -->
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">{{ $internetOnlyClients["current"] }}</h5>
                        <i class="fas fa-wifi"></i>
                    </div>
                    <small class="text-muted d-block">Clientes con Internet</small>
                    <hr> <!-- Línea divisoria -->
                    <div class="d-flex justify-content-between">
                        <small class="text-muted">Mes anterior: {{  $internetOnlyClients["previous"] }}</small>
                        <div class="{{  $internetOnlyClients["percentageChange"] < 0 ? 'text-danger' : 'text-success' }}">

                            <i class="fas fa-arrow-{{  $internetOnlyClients["percentageChange"] < 0 ? 'down' : 'up' }}"></i>
                            {{ number_format(abs( $internetOnlyClients["percentageChange"]), 2) }}%

                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tarjeta 3: Clientes con TV -->
        <div class="col-md-3">
            <div class="card custom-card"> <!-- Usamos la clase personalizada -->
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">{{  $tvOnlyClients["current"] }}</h5>
                        <i class="fas fa-tv"></i>
                    </div>
                    <small class="text-muted d-block">Clientes con TV</small>
                    <hr> <!-- Línea divisoria -->
                    <div class="d-flex justify-content-between">
                        <small class="text-muted">Mes anterior: {{  $tvOnlyClients["previous"] }}</small>
                        <div class="{{  $tvOnlyClients["percentageChange"] < 0 ? 'text-danger' : 'text-success' }}">

                            <i class="fas fa-arrow-{{ $tvOnlyClients["percentageChange"] < 0 ? 'down' : 'up' }}"></i>
                            {{ number_format(abs( $tvOnlyClients["percentageChange"]), 2) }}%

                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tarjeta 4: Clientes con Combo -->
        <div class="col-md-3">
            <div class="card custom-card"> <!-- Usamos la clase personalizada -->
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">{{ $bothServicesClients["current"] }}</h5>
                        <i class="fas fa-box"></i>
                    </div>
                    <small class="text-muted d-block">Clientes con Combo</small>
                    <hr> <!-- Línea divisoria -->
                    <div class="d-flex justify-content-between">
                        <small class="text-muted">Mes anterior: {{ $bothServicesClients["previous"] }}</small>
                        <div class="{{ $bothServicesClients["percentageChange"] < 0 ? 'text-danger' : 'text-success' }}">

                            <i class="fas fa-arrow-{{ $bothServicesClients["percentageChange"] < 0 ? 'down' : 'up' }}"></i>
                            {{ number_format(abs($bothServicesClients["percentageChange"]), 2) }}%

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row m-2">
        <div class="col-12">
            <div class="card custom-card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-12">
                            <h2 class="text-center mt-5">Gráfica de Clientes</h2>
                            <canvas id="clientesChart" height="100"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
    <!-- Incluir Chart.js desde CDN -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const ctx = document.getElementById('clientesChart').getContext('2d');
        const clientesChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: @json($data['months']),
                datasets: [
                    {
                        label: 'Activos',
                        data: @json($data['activos']),
                        backgroundColor: 'rgba(54, 162, 235, 0.6)',
                        borderColor: 'rgba(54, 162, 235, 1)',
                        borderWidth: 1
                    },
                    {
                        label: 'Con Internet',
                        data: @json($data['con_internet']),
                        backgroundColor: 'rgba(75, 192, 192, 0.6)',
                        borderColor: 'rgba(75, 192, 192, 1)',
                        borderWidth: 1
                    },
                    {
                        label: 'Con TV',
                        data: @json($data['con_tv']),
                        backgroundColor: 'rgba(153, 102, 255, 0.6)',
                        borderColor: 'rgba(153, 102, 255, 1)',
                        borderWidth: 1
                    },
                    {
                        label: 'Con Combo',
                        data: @json($data['con_combo']),
                        backgroundColor: 'rgba(255, 159, 64, 0.6)',
                        borderColor: 'rgba(255, 159, 64, 1)',
                        borderWidth: 1
                    }
                ]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Nro. de clientes'
                        }
                    },
                    x: {
                        title: {
                            display: true,
                            text: 'Meses del Año {{ $year }}'
                        }
                    }
                },
                plugins: {
                    title: {
                        display: true,
                        text: 'Estadística del Año {{ $year }}'
                    }
                }
            }
        });
    </script>
@endsection
