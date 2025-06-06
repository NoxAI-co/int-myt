@extends('layouts.app')

@section('style')
<link href="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.min.css" rel="stylesheet">
<style>
    .notice {
        padding: 15px;
        background-color: #fafafa;
        border-left: 6px solid #7f7f84;
        margin-bottom: 10px;
        -webkit-box-shadow: 0 5px 8px -6px rgba(0, 0, 0, .2);
        -moz-box-shadow: 0 5px 8px -6px rgba(0, 0, 0, .2);
        box-shadow: 0 0 0 0 rgba(0, 0, 0, 0);
    }

    .notice-sm {
        padding: 10px;
        font-size: 80%;
    }

    .notice-lg {
        padding: 35px;
        font-size: large;
    }

    .notice-success {
        border-color: #80D651;
    }

    .notice-success>strong {
        color: #80D651;
    }

    .notice-info {
        border-color: #267eb5;
    }

    .notice-info>strong {
        color: #45ABCD;
    }

    .notice-warning {
        border-color: #FEAF20;
    }

    .notice-warning>strong {
        color: #FEAF20;
    }

    .notice-danger {
        border-color: #d73814;
    }

    .notice-danger>strong {
        color: #d73814;
    }

    .ai-icon-container {
        width: 48px;
        height: 48px;
        border-radius: 50%;
        background: linear-gradient(135deg, #6e3fc3, #31a2f0);
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 0 15px rgba(110, 63, 195, 0.3);
        position: relative;
    }

    .ai-icon {
        font-size: 24px;
        color: white;
        animation: pulse 2s infinite;
    }

    @keyframes pulse {
        0% {
            transform: scale(1);
            opacity: 1;
        }

        50% {
            transform: scale(1.2);
            opacity: 0.8;
        }

        100% {
            transform: scale(1);
            opacity: 1;
        }
    }

    .card-counter {
        position: relative;
        display: grid;
        grid-template-columns: auto 1fr;
        grid-gap: 10px;
        align-items: center;
        padding: 15px;
        margin-bottom: 15px;
        min-height: 80px;
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        overflow: hidden;
    }

    .card-counter:hover {
        transform: translateY(-5px);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
    }

    .card-counter.primary {
        background-color: #007bff;
        color: white;
    }

    .card-counter.success {
        background-color: #28a745;
        color: white;
    }

    .card-counter.warning {
        background-color: #f6c23e;
        color: white;
    }

    .card-counter.info {
        background-color: #17a2b8;
        color: white;
    }

    .card-counter i {
        grid-row: span 2;
        font-size: 2em;
        opacity: 0.9;
        margin-right: 5px;
        display: flex;
        align-items: center;
        justify-content: center;
        width: 40px;
    }

    .card-counter .count-numbers {
        font-size: 16px;
        font-weight: 600;
        line-height: 1.2;
        margin: 0;
        padding: 0;
        text-align: right;
        width: 100%;
    }

    .card-counter .count-name {
        font-size: 14px;
        line-height: 1.2;
        margin: 0;
        padding: 0;
        opacity: 0.95;
        font-weight: 500;
        text-align: right;
        width: 100%;
    }

    .dash-card {
        background: white;
        border-radius: 4px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.12);
        margin-bottom: 20px;
        padding: 20px;
    }

    .dash-card-header {
        border-bottom: 1px solid #eee;
        margin-bottom: 15px;
        padding-bottom: 10px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .dash-card-header h4 {
        margin: 0;
        color: #333;
    }

    .shortcuts-section {
        background: #f8f9fc;
        border-radius: 8px;
        padding: 20px;
        margin-bottom: 30px;
    }

    hr {
        margin-top: 1rem;
        margin-bottom: 1rem;
        border: 0;

        border-top: 1px solid {
                {
                Auth: :user()->rol > 1 ? Auth::user()->empresa()->color:''
            }
        }

        ;
    }

    @media (max-width: 1200px) {
        .card-counter {
            padding: 12px;
        }

        .card-counter i {
            font-size: 1.8em;
            width: 35px;
        }
    }

    @media (max-width: 992px) {
        .col-lg-2 {
            flex: 0 0 33.333333%;
            max-width: 33.333333%;
        }
    }

    @media (max-width: 768px) {
        .col-lg-2 {
            flex: 0 0 50%;
            max-width: 50%;
        }

        .card-counter {
            min-height: 70px;
        }
    }

    @media (max-width: 576px) {
        .col-lg-2 {
            flex: 0 0 100%;
            max-width: 100%;
        }

        .card-counter {
            grid-template-columns: 40px 1fr;
            padding: 10px;
        }

        .card-counter i {
            font-size: 1.6em;
            width: 30px;
        }
    }
</style>
@endsection

@section('content')
@if(auth()->user()->modo_lectura())
<div class="alert alert-warning text-left" role="alert">
    <h4 class="alert-heading text-uppercase">Integra Colombia: Suscripción Vencida</h4>
    <p>Si desea seguir disfrutando de nuestros servicios adquiera alguno de nuestros planes.</p>
    <p>Medios de pago Nequi: 3026003360 Cuenta de ahorros Bancolombia 42081411021 CC 1001912928 Ximena Herrera representante legal. Adjunte su pago para reactivar su membresía</p>
</div>
@endif

@if(isset($_SESSION['permisos']['113']) && Auth::user()->rol != 8)
<div class="row">
    <!-- Header Dashboard -->
    <div class="col-12">
        <div class="dash-card mb-4">
            <div class="dash-card-header">
                <div class="d-flex align-items-center">
                    <div class="ai-icon-container mr-3">
                        <i class="fas fa-robot ai-icon"></i>
                    </div>
                    <div>
                        <h4>Dashboard General</h4>
                        <p class="text-muted mb-0">Resumen de actividad del sistema</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Nueva sección de atajos -->
    <div class="col-12">
        <div class="shortcuts-section">
            <h5 class="shortcuts-title mb-4">
                <i class="fas fa-bolt mr-2"></i>Accesos Rápidos
            </h5>
            <div class="row justify-content-center">
                @if(isset($_SESSION['permisos']['2']))
                <div class="col-lg-2 col-md-4 col-sm-6">
                    <a href="{{route('contactos.create')}}" class="text-decoration-none d-block">
                        <div class="card-counter success">
                            <i class="fas fa-users"></i>
                            <span class="count-numbers">Nuevo</span>
                            <span class="count-name">Cliente</span>
                        </div>
                    </a>
                </div>
                @endif

                @if(isset($_SESSION['permisos']['411']))
                <div class="col-lg-2 col-md-4 col-sm-6">
                    <a href="{{route('contratos.create')}}" class="text-decoration-none d-block">
                        <div class="card-counter info">
                            <i class="fas fa-file-contract"></i>
                            <span class="count-numbers">Nuevo</span>
                            <span class="count-name">Contrato</span>
                        </div>
                    </a>
                </div>
                @endif

                @if(isset($_SESSION['permisos']['202']))
                <div class="col-lg-2 col-md-4 col-sm-6">
                    <a href="{{route('radicados.create')}}" class="text-decoration-none d-block">
                        <div class="card-counter warning">
                            <i class="fas fa-ticket-alt"></i>
                            <span class="count-numbers">Nuevo</span>
                            <span class="count-name">Radicado</span>
                        </div>
                    </a>
                </div>
                @endif

                @if(isset($_SESSION['permisos']['42']))
                <div class="col-lg-2 col-md-4 col-sm-6">
                    <a href="{{route('facturas.create')}}" class="text-decoration-none d-block">
                        <div class="card-counter primary">
                            <i class="fas fa-file-invoice-dollar"></i>
                            <span class="count-numbers">Nueva</span>
                            <span class="count-name">Factura</span>
                        </div>
                    </a>
                </div>
                @endif

                @if(isset($_SESSION['permisos']['420']))
                <div class="col-lg-2 col-md-4 col-sm-6">
                    <a href="{{route('facturas.create-electronica')}}" class="text-decoration-none d-block">
                        <div class="card-counter success">
                            <i class="fas fa-file-invoice"></i>
                            <span class="count-numbers">Nueva</span>
                            <span class="count-name">Factura Electrónica</span>
                        </div>
                    </a>
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Métricas principales -->
    <div class="col-md-3">
        <div class="dash-card">
            <i class="fas fa-file-contract float-right text-primary" style="font-size: 24px;"></i>
            <h3>{{ $contra_ena + $contra_disa }}</h3>
            <div>Contratos Totales</div>
            <div class="metric-small">
                <span>{{ $contra_ena }} Habilitados</span> |
                <span>{{ $contra_disa }} Deshabilitados</span>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="dash-card">
            <i class="fas fa-file-invoice-dollar float-right text-success" style="font-size: 24px;"></i>
            <h3>{{ $factura }}</h3>
            <div>Facturas Totales</div>
            <div class="metric-small">
                <span>{{ $factura_cerrada }} Cerradas</span> |
                <span>{{ $factura_abierta }} Abiertas</span>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="dash-card">
            <i class="fas fa-ticket-alt float-right text-warning" style="font-size: 24px;"></i>
            <h3>{{ $radicados }}</h3>
            <div>Radicados Totales</div>
            <div class="metric-small">
                <span>{{ $radicados_solventado }} Solventados</span> |
                <span>{{ $radicados_pendiente }} Pendientes</span>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="dash-card">
            <i class="fas fa-tv float-right text-info" style="font-size: 24px;"></i>
            <h3>{{ $contratosCatv }}</h3>
            <div>Contratos CATV</div>
            <div class="metric-small">
                <span>{{ $contratosCatvEnabled }} Habilitados</span> |
                <span>{{ $contratosCatvDisabled }} Deshabilitados</span>
            </div>
        </div>
    </div>

    <!-- Gráficas -->
    <div class="col-md-6">
        <div class="dash-card">
            <div class="dash-card-header">
                <h4>Estado de Contratos</h4>
            </div>
            <div id="contractStatus" style="height: 250px;"></div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="dash-card">
            <div class="dash-card-header">
                <h4>Estado de Facturación</h4>
            </div>
            <div id="billingStatus" style="height: 250px;"></div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="dash-card">
            <div class="dash-card-header">
                <h4>Estado de Radicados</h4>
            </div>
            <div id="ticketStatus" style="height: 250px;"></div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="dash-card">
            <div class="dash-card-header">
                <h4>Estado CATV</h4>
            </div>
            <div id="catvStatus" style="height: 250px;"></div>
        </div>
    </div>

    <!-- Tendencias Mensuales -->
    <div class="col-md-12">
        <div class="dash-card">
            <div class="dash-card-header">
                <h4>Tendencias Mensuales</h4>
            </div>
            <div id="monthlyTrends" style="height: 250px;"></div>
        </div>
    </div>
    <div class="col-12">
        <div class="dash-card mb-4">
            <div class="dash-card-header">
                <div>
                    <h4>Resumen de Facturación</h4>
                    <p class="text-muted mb-0">Estado actual de facturación</p>
                </div>
            </div>
            <div class="row mt-4">
                <!-- Total Facturado -->
                <div class="col-md-6 col-lg-3 mb-4">
                    <div class="card-counter primary">
                        <i class="fas fa-dollar-sign"></i>
                        <div>
                            <span class="count-numbers">{{ number_format($total_facturado, 2) }}</span>
                            <span class="count-name">Total Facturado</span>
                        </div>
                    </div>
                </div>

                <!-- Facturas Electrónicas -->
                <div class="col-md-6 col-lg-3 mb-4">
                    <div class="card-counter info">
                        <i class="fas fa-file-invoice"></i>
                        <div>
                            <span class="count-numbers">{{ $facturas_electronicas }}</span>
                            <span class="count-name">Facturas Electrónicas</span>
                        </div>
                    </div>
                </div>

                <!-- Facturas Pendientes -->
                <div class="col-md-6 col-lg-3 mb-4">
                    <div class="card-counter warning">
                        <i class="fas fa-clock"></i>
                        <div>
                            <span class="count-numbers">{{ $factura_abierta }}</span>
                            <span class="count-name">Facturas Pendientes</span>
                        </div>
                    </div>
                </div>

                <!-- Facturas Pagadas -->
                <div class="col-md-6 col-lg-3 mb-4">
                    <div class="card-counter success">
                        <i class="fas fa-check-circle"></i>
                        <div>
                            <span class="count-numbers">{{ $factura_cerrada }}</span>
                            <span class="count-name">Facturas Pagadas</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endif

@if(Auth::user()->rol == 8)
<div class="row card-description">
    <form action="https://checkout.wompi.co/p/" method="GET" id="form-wompi" class="d-none">
        <input type="hidden" name="public-key" value="{{env('WOMPI_KEY')}}" />
        <input type="hidden" name="currency" value="COP" />
        <input type="hidden" name="amount-in-cents" id="amount-in-cents" />
        <input type="hidden" name="reference" value="{{str_replace(' ', '_', Auth::user()->nombres)}}<?php echo '_IST_' . rand(); ?>" />
        <input type="hidden" name="redirect-url" value="https://istingenieria.online/RecargaWompi" />
        <button class="btn btn-success" type="submit" disabled>Pagar con Wompi</button>
    </form>

    <div class="col-md-4 offset-md-4" style="text-align:center;">
        <div class="contact-form">
            <h4>RECARGA SALDO CON WOMPI</h4>
            <input type="number" min="1" class="form-control my-3" id="recarga" value="0">
            <button class="btn btn-success" type="submit" onclick="confirmarp('form-wompi');" disabled>RECARGAR</button>
        </div>
    </div>
</div>
@endif

<input type="hidden" id="simbolo" value="$">

<!-- Scripts para las gráficas -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/raphael/2.3.0/raphael.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/morris.js/0.5.1/morris.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        if (typeof Morris === 'undefined') {
            console.error('Morris.js no está cargado');
            return;
        }

        // Estado de Contratos
        new Morris.Donut({
            element: 'contractStatus',
            data: [{
                    label: 'Habilitados',
                    value: @json($contra_ena)
                },
                {
                    label: 'Deshabilitados',
                    value: @json($contra_disa)
                }
            ],
            colors: ['#1cc88a', '#e74a3b'],
            labelColor: '#3d4651',
            backgroundColor: '#fff',
            resize: true
        });

        // Estado de Facturación
        new Morris.Donut({
            element: 'billingStatus',
            data: [{
                    label: 'Cerradas',
                    value: @json($factura_cerrada)
                },
                {
                    label: 'Abiertas',
                    value: @json($factura_abierta)
                }
            ],
            colors: ['#1cc88a', '#e74a3b'],
            labelColor: '#3d4651',
            backgroundColor: '#fff',
            resize: true
        });

        // Estado de Radicados
        new Morris.Donut({
            element: 'ticketStatus',
            data: [{
                    label: 'Solventados',
                    value: @json($radicados_solventado)
                },
                {
                    label: 'Pendientes',
                    value: @json($radicados_pendiente)
                }
            ],
            colors: ['#1cc88a', '#e74a3b'],
            labelColor: '#3d4651',
            backgroundColor: '#fff',
            resize: true
        });

        // Estado CATV
        new Morris.Donut({
            element: 'catvStatus',
            data: [{
                    label: 'Habilitados',
                    value: @json($contratosCatvEnabled)
                },
                {
                    label: 'Deshabilitados',
                    value: @json($contratosCatvDisabled)
                }
            ],
            colors: ['#1cc88a', '#e74a3b'],
            labelColor: '#3d4651',
            backgroundColor: '#fff',
            resize: true
        });

        // Tendencias Mensuales
        new Morris.Line({
            element: 'monthlyTrends',
            data: [{
                    month: 'Ene',
                    contratos: @json($contra_ena),
                    facturas: @json($factura)
                },
                {
                    month: 'Feb',
                    contratos: @json($contra_ena + 5),
                    facturas: @json($factura + 3)
                },
                {
                    month: 'Mar',
                    contratos: @json($contra_ena + 8),
                    facturas: @json($factura + 7)
                },
                {
                    month: 'Abr',
                    contratos: @json($contra_ena + 12),
                    facturas: @json($factura + 10)
                },
                {
                    month: 'May',
                    contratos: @json($contra_ena + 15),
                    facturas: @json($factura + 14)
                },
                {
                    month: 'Jun',
                    contratos: @json($contra_ena + 20),
                    facturas: @json($factura + 18)
                }
            ],
            xkey: 'month',
            ykeys: ['contratos', 'facturas'],
            labels: ['Contratos Activos', 'Facturas Generadas'],
            lineColors: ['#4e73df', '#1cc88a'],
            pointSize: 4,
            hideHover: 'auto',
            resize: true,
            lineWidth: 3,
            smooth: false,
            gridLineColor: '#e3e6f0',
            gridTextColor: '#858796',
            gridTextSize: 12,
            gridTextFamily: 'Open Sans',
            parseTime: false
        });
    });

    function confirmarp(form, mensaje = "Lo vamos a redirigir a la pasarela de pago WOMPI para realizar la recarga", submensaje = '¿Desea continuar?', confirmar = 'Si') {
        if ($("#buyerFullName").val() != '') {
            swal({
                title: mensaje,
                text: submensaje,
                type: 'question',
                showCancelButton: true,
                confirmButtonColor: '#00ce68',
                cancelButtonColor: '#d33',
                confirmButtonText: confirmar,
                cancelButtonText: 'No',
            }).then((result) => {
                if (result.value) {
                    var monto_pago = $("#recarga").val();
                    $("#amount-in-cents").val(monto_pago + '00');
                    document.getElementById(form).submit();
                }
            });
        } else {
            swal({
                title: 'Debe llenar la información solicitada',
                text: submensaje,
                type: 'warning',
                showCancelButton: true,
                showConfirmButton: false,
                cancelButtonColor: '#00ce68',
                cancelButtonText: 'Aceptar',
            })
        }
    }
</script>
@endsection