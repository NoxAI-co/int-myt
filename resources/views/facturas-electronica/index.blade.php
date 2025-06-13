@extends('layouts.app')


@section('boton')
    @if(auth()->user()->modo_lectura())
	    <div class="alert alert-warning text-left" role="alert">
	        <h4 class="alert-heading text-uppercase">Integra Colombia: Suscripción Vencida</h4>
	       <p>Si desea seguir disfrutando de nuestros servicios adquiera alguno de nuestros planes.</p>
<p>Medios de pago Nequi: 3026003360 Cuenta de ahorros Bancolombia 42081411021 CC 1001912928 Ximena Herrera representante legal. Adjunte su pago para reactivar su membresía</p>
	    </div>
	@else
        <a href="javascript:abrirFiltrador()" class="btn btn-info btn-sm my-1" id="boton-filtrar"><i class="fas fa-search"></i>Filtrar</a>
		<?php if(isset($_SESSION['permisos']['853'])){ ?>
		<a href="{{route('facturas.create-electronica')}}" class="btn btn-primary btn-sm"><i class="fas fa-plus"></i> Nueva Factura Electrónica</a>
		<?php } ?>
    @endif
@endsection

@section('content')
    @if(Session::has('success'))
        <div class="alert alert-success">
        	{{Session::get('success')}}
        </div>
        <script type="text/javascript">
        	setTimeout(function() {
        		$('.alert').hide();
        		$('.active_table').attr('class', ' ');
        	}, 5000);
        </script>
    @endif

	@if(Session::has('error'))
	<div class="alert alert-danger" >
		{{Session::get('error')}}
	</div>

	<script type="text/javascript">
		setTimeout(function(){
			$('.alert').hide();
			$('.active_table').attr('class', ' ');
		}, 8000);
	</script>
	@endif

    @if(Session::has('message_denied'))
	    <div class="alert alert-danger" role="alert">
	    	{{Session::get('message_denied')}}
	    	@if(Session::get('errorReason'))<br> <strong>Razon(es): <br></strong>
	    	    @if(is_string(Session::get('errorReason')))
	    	        {{Session::get('errorReason')}}
	    	    @elseif (count(Session::get('errorReason')) >= 1)
	    	        @php $cont = 0 @endphp
	    	        @foreach(Session::get('errorReason') as $error)
	    	            @php $cont = $cont + 1; @endphp
	    	            {{$cont}} - {{$error}} <br>
	    	        @endforeach
	    	    @endif
	    	@endif
	    	<button type="button" class="close" data-dismiss="alert" aria-label="Close">
	    		<span aria-hidden="true">&times;</span>
	    	</button>
	    </div>
	@endif

	@if(Session::has('message_success'))
	    <div class="alert alert-success" role="alert">
	    	{{Session::get('message_success')}}
	    	<button type="button" class="close" data-dismiss="alert" aria-label="Close">
	    		<span aria-hidden="true">&times;</span>
	    	</button>
	    </div>
	@endif

	<div class="container-fluid d-none" id="form-filter">
		<fieldset>
            <legend>Filtro de Búsqueda</legend>
			<div class="card shadow-sm border-0">
				<div class="card-body pb-3 pt-2" style="background: #f9f9f9;">
					<div class="row">
						<div class="col-md-2 pl-1 pt-1">
							<input type="text" placeholder="Nro" id="codigo" class="form-control rounded">
						</div>
						<div class="col-md-2 pl-1 pt-1">
							<select title="Cliente" class="form-control rounded selectpicker" id="cliente" data-size="5" data-live-search="true">
								@foreach ($clientes as $cliente)
									<option value="{{ $cliente->id}}">{{ $cliente->nombre}} {{$cliente->apellido1}} {{$cliente->apellido2}} - {{ $cliente->nit}}</option>
								@endforeach
							</select>
						</div>
						<div class="col-md-2 pl-1 pt-1">
							<select title="Municipio" class="form-control rounded selectpicker" id="municipio" data-size="5" data-live-search="true">
								@foreach ($municipios as $municipio)
									<option value="{{ $municipio->id}}">{{ $municipio->nombre}}</option>
								@endforeach
							</select>
						</div>
						<div class="col-md-2 pl-1 pt-1 position-relative">
                            <input type="date" id="creacion" name="creacion" class="form-control rounded" autocomplete="off">
                            <label for="creacion" class="placeholder">Creación</label>
                        </div>
                        <div class="col-md-2 pl-1 pt-1 position-relative">
                            <input type="date" id="vencimiento" name="vencimiento" class="form-control rounded" autocomplete="off">
                            <label for="vencimiento" class="placeholder">Vencimiento</label>
                        </div>
						<div class="col-md-2 pl-1 pt-1">
							<select title="Servidor" class="form-control rounded selectpicker" id="servidor">
								@foreach ($servidores as $servidor)
								<option value="{{ $servidor->id}}">{{ $servidor->nombre}}</option>
								@endforeach
							</select>
						</div>
						<div class="col-md-2 pl-1 pt-1">
							<select title="Estado" class="form-control rounded selectpicker" id="estado">
								<option value="1">Abierta</option>
								<option value="A">Cerrada</option>
								<option value="2">Anulada</option>
							</select>
						</div>
						<div class="col-md-2 pl-1 pt-1">
							<select title="Emisión" class="form-control rounded selectpicker" id="emision">
								<option value="1">Emitida</option>
								<option value="0">No emitida</option> 	
								<option value="2">Error emisión</option> 	
							</select>
						</div>
						<div class="col-md-2 pl-1 pt-1 d-none">
							<select title="Enviada a Correo" class="form-control rounded selectpicker" id="correo">
								<option value="1">Si</option>
								<option value="A">No</option>
							</select>
						</div>
					</div>

					<div class="row">
						<div class="col-md-12 pl-1 pt-1 text-center">
							<a href="javascript:limpiarFiltrador()" class="btn btn-icons ml-1 btn-outline-danger rounded btn-sm p-1" title="Limpiar parámetros de busqueda"><i class="fas fa-times"></i></a>
							<a href="javascript:void(0)" id="filtrar" class="btn btn-icons btn-outline-info rounded btn-sm p-1" title="Iniciar busqueda avanzada"><i class="fas fa-search"></i></a>
							<a href="javascript:exportar()" class="btn btn-icons mr-1 btn-outline-success rounded btn-sm p-1" title="Exportar"><i class="fas fa-file-excel"></i></a>
						</div>
					</div>
				</div>
			</div>
		</fieldset>
	</div>

	<div class="row card-description">
		<div class="col-md-12">
    		<div class="container-filtercolumn form-inline">
    			@if(isset($_SESSION['permisos']['750']))
    			<a href="{{route('campos.organizar', 4)}}" class="btn btn-warning btn-sm mr-1"><i class="fas fa-table"></i> Organizar Tabla</a>
    			@endif
    			@if(Auth::user()->empresa()->efecty == 1)
    			<a href="{{route('facturas.downloadefecty')}}" class="btn btn-warning btn-sm mr-1" style="background: #938B16; border: solid #938B16 1px;"><i class="fas fa-cloud-download-alt"></i> Descargar Archivo Efecty</a>
    			@endif
    			@if(isset($_SESSION['permisos']['774']))
                <a href="{{route('promesas-pago.index')}}" class="btn btn-outline-danger btn-sm mr-1"><i class="fas fa-calendar"></i> Ver Promesas de Pago</a>
                @endif
				<div class="dropdown mr-1">
                    <button class="btn btn-warning dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        Acciones en Lote
                    </button>
                    <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                        <a class="dropdown-item" href="javascript:void(0)" id="btn_emitir"><i class="fas fa-server"></i> Emitir Facturas en Lotess</a>
						<a class="dropdown-item" href="javascript:void(0)" id="btn_siigo"><i class="fas fa-server"></i> Enviar a Siigo en lote</a>
						<a class="dropdown-item" href="javascript:void(0)" id="btn_imp_fac"><i class="fas fa-file-excel"></i> Imprimir facturas</a>
					</div>
                </div>
			</div>
		</div>
		<div class="col-md-12">
			<table class="table table-striped table-hover w-100" id="tabla-facturas">
				<thead class="thead-dark">
					<tr>
						@foreach($tabla as $campo)
    					    <th>{{$campo->nombre}}</th>
    					@endforeach
						<th>Acciones</th>
					</tr>
				</thead>
			</table>
		</div>
	</div>
	
	<div class="modal fade" id="promesaPago" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">GENERAR PROMESA DE PAGO</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div id="div_promesa"></div>
            </div>
        </div>
    </div>

	{{-- MODAL ENVIO SIIGO --}}
	<div class="modal fade" id="envio_siigo" role="dialog">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<h4 class="modal-title">Envio a Siigo</h4>
					<button type="button" class="close" data-dismiss="modal">&times;</button>
				</div>
				<div class="modal-body">

					<div class="alert alert-info" role="alert">
						El código de los ítems no puede tener comillas simples (') ni espacios y la referencia no puede superar los 30 caracteres.
					</div>

					<form method="POST" action="" style="padding: 2% 3%;" role="form"
					class="forms-sample" novalidate id="form" >

						{{ csrf_field() }}

						<input type="hidden" id='factura_id'>

						<div class="card mb-4 p-2">
							<h6 class="mb-0" id="h4-factnro"></h6>
						</div>

						<div class="row">
							<div class="col-md-12 form-group">
								<label class="control-label">Tipo Comprobante Siigo</label>
								<select class="form-control" name="tipo_comprobante_siigo" id="tipo_comprobante_siigo">

								</select>
							</div>
						</div>

						<div class="row">
							<div class="col-md-6 form-group">
								<label class="control-label">Fecha</label>
								<input class="form-control" type="text" id="fecha_siigo" readonly>
							</div>

							<div class="col-md-6 form-group">
								<label class="control-label">Cliente</label>
								<input class="form-control" type="text" id="cliente_siigo" readonly>
							</div>

						</div>

						<div class="row">
							<div class="col-md-6 form-group">
								<label class="control-label">Centro Costos Siigo</label>
								<select class="form-control" name="centro_costos" id="centro_costos">

								</select>
							</div>

							<div class="col-md-6 form-group">
								<label class="control-label">Tipos de Pago Siigo</label>
								<select class="form-control" name="tipos_pago" id="tipos_pago">

								</select>
							</div>

						</div>

						<div class="row">
							<div class="col-md-6 form-group">
								<label class="control-label">Usuarios Siigo</label>
								<select class="form-control" name="usuarios" id="usuarios">

								</select>
							</div>

						</div>
					</form>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
					<a href="javascript:sendInvoiceSiigo()" class="btn btn-success">Guardar</A>
				</div>
			</div>
		</div>
	</div>
	{{-- /MODAL ENVIO SIIGO --}}
@endsection

@section('style')
<style>
    .placeholder {
        position: absolute;
        top: 50%;
        left: 10px;
        transform: translateY(-50%);
        color: #6c757d;
        pointer-events: none;
        transition: all 0.2s ease-in-out;
    }
    input:focus + .placeholder,
    input:not(:placeholder-shown) + .placeholder {
        top: 5px;
        font-size: 12px;
        color: #495057;
    }
</style>
@endsection

@section('scripts')
<script>
	function showModalSiigo(factura_id,codigo,fecha,cliente){

if (window.location.pathname.split("/")[1] === "software") {
	var url='/software/siigo/get_modal_invoice';
}else{
	var url = '/siigo/get_modal_invoice';
}

$("#envio_siigo").modal('show');
$("#tipo_comprobante_siigo").empty();
$("#centro_costos").empty();
$("#tipos_pago").empty();
$("#usuarios").empty();

$.ajax({
	url: url,
	headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
	method: 'get',
	data: {
		factura_id: factura_id,
	},
	success: function (data) {

		if(data.status == 200){

			$("#fecha_siigo").val(fecha);
			$("#cliente_siigo").val(cliente);
			$("#factura_id").val(factura_id);

			$.each(data.tipos_comprobante, function(index, item) {

				$("#tipo_comprobante_siigo").append(
					$('<option>', {
						value: item.id,
						text: item.name + " - " + item.description
					})
				);

			});

			$.each(data.centro_costos, function(index, item) {

			$("#centro_costos").append(
				$('<option>', {
					value: item.id,
					text: item.name + " - " + item.description
				})
			);

			});

			$("#tipos_pago").append($('<option>', {
					value: 0,
					text: "Seleccione tipo de pago"
				}));

			$.each(data.tipos_pago, function(index, item) {
			$("#tipos_pago").append(
				$('<option>', {
					value: item.id,
					text: item.name
				})
			);
			});


			$("#usuarios").append($('<option>', {
					value: 0,
					text: "Seleccione un usuario"
				}));

			$.each(data.usuarios, function(index, item) {
			$("#usuarios").append(
				$('<option>', {
					value: item.id,
					text: item.first_name + " " + item.last_name
				})
			);
			});


			$("#h4-factnro").text("Codigo Factura: " + codigo);
			$("#envio_siigo").modal('show');
		}else{
			alert("Ha ocurrido un error");
		}
	}
	});
	}

	function sendInvoiceSiigo(){

	if (window.location.pathname.split("/")[1] === "software") {
		var url='/software/siigo/send_invoice';
	}else{
		var url = '/siigo/send_invoice';
	}

	let tipo_comprobante = $("#tipo_comprobante_siigo").val();
	let factura_id = $("#factura_id").val();
	let tipos_pago = $("#tipos_pago").val();
	let centro_costos = $("#centro_costos").val();
	let usuario = $("#usuarios").val();

	$.ajax({
		url: url,
		headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
		method: 'get',
		data: {
			tipo_comprobante,
			factura_id,
			tipos_pago,
			centro_costos,
			usuario
		},
		success: function (data) {

			if (data.status == 200) {
				swal({
					title: 'ÉXITO',
					html: 'Factura enviada a Siigo correctamente.',
					type: 'success',
					showConfirmButton: true,
					confirmButtonColor: '#1A59A1',
					confirmButtonText: 'ACEPTAR',
				}).then(() => {
					$("#envio_siigo").modal('hide');
					getDataTable();
				});
			}else if(data.status == 400){
				swal({
					title: 'ERROR',
					html: data.error,
					type: 'error',
					showConfirmButton: true,
					confirmButtonColor: '#d33',
					confirmButtonText: 'ACEPTAR',
				});
			} else {
				swal({
					title: 'ERROR',
					html: 'No se pudo enviar la factura a Siigo. Por favor, inténtelo de nuevo más tarde.',
					type: 'error',
					showConfirmButton: true,
					confirmButtonColor: '#d33',
					confirmButtonText: 'ACEPTAR',
				});
			}
		}, // <- esta coma es esencial
		error: function (xhr, status, error) {
			console.error("Error al enviar la factura a Siigo:", error);
			swal({
				title: 'ERROR',
				html: 'No se pudo enviar la factura a Siigo. Por favor, inténtelo de nuevo más tarde.',
				type: 'error',
				showConfirmButton: true,
				confirmButtonColor: '#d33',
				confirmButtonText: 'ACEPTAR',
			});
		}
	});
	}

	var tabla = $('#tabla-facturas');
	window.addEventListener('load', function() {
		var tabla = $('#tabla-facturas').DataTable({
			responsive: true,
			serverSide: true,
			processing: true,
			searching: false,
			language: {
				'url': '/vendors/DataTables/es.json'
			},
			order: [
				[4, "DESC"],[0, "DESC"]
			],
			"pageLength": {{ Auth::user()->empresa()->pageLength }},
			ajax: '{{url("/facturas-electronicas")}}',
			headers: {
				'X-CSRF-TOKEN': '{{csrf_token()}}'
			},
			@if(isset($_SESSION['permisos']['830']))
			select: true,
            select: {
                style: 'multi',
            },
            dom: 'Blfrtip',
            buttons: [{
                text: '<i class="fas fa-check"></i> Seleccionar todos',
                action: function() {
                    tabla.rows({
                        page: 'current'
                    }).select();
                }
            },
            {
                text: '<i class="fas fa-times"></i> Deseleccionar todos',
                action: function() {
                    tabla.rows({
                        page: 'current'
                    }).deselect();
                }
            }],
            @endif
			columns: [
				@foreach($tabla as $campo)
                {data: '{{$campo->campo}}'},
                @endforeach
				{data: 'acciones'},
			]
		});

		tabla.on('preXhr.dt', function(e, settings, data) {
			data.codigo = $('#codigo').val();
			data.corte = $('#corte').val();
			data.cliente = $('#cliente').val();
			data.municipio = $('#municipio').val();
			data.vendedor = $('#vendedor').val();
			data.creacion = $('#creacion').val();
			data.vencimiento = $('#vencimiento').val();
			data.comparador = $('#comparador').val();
			data.total = $('#total').val();
			data.servidor = $('#servidor').val();
			data.estado = $('#estado').val();
			data.emision = $('#emision').val();
			data.filtro = true;
		});

		$('#filtrar').on('click', function(e) {
			getDataTable();
			return false;
		});

		$('#form-filter').on('keypress', function(e) {
			if (e.which == 13) {
				getDataTable();
				return false;
			}
		});

		$('#codigo').on('keyup',function(e) {
            if(e.which > 32 || e.which == 8) {
                getDataTable();
                return false;
            }
        });

        $('#cliente, #municipio, #estado, #correo, #creacion, #vencimiento').on('change',function() {
            getDataTable();
            return false;
        });

		$('.vencimiento').datepicker({
			locale: 'es-es',
      		uiLibrary: 'bootstrap4',
			format: 'yyyy-mm-dd' ,
		});

		$('.creacion').datepicker({
			locale: 'es-es',
      		uiLibrary: 'bootstrap4',
			format: 'yyyy-mm-dd' ,
		});

		$('#tabla-facturas tbody').on('click', 'tr', function () {
			var table = $('#tabla-facturas').DataTable();
			var nro = table.rows('.selected').data().length;

			if(table.rows('.selected').data().length >= 0){
				$("#btn_emitir").removeClass('disabled d-none');
				$("#btn_imp_fac").removeClass('disabled d-none');
			}else{
				$("#btn_emitir").addClass('disabled d-none');
				$("#btn_imp_fac").removeClass('disabled d-none');
			}
        });

        $('#btn_emitir').on('click', function(e) {
            var table = $('#tabla-facturas').DataTable();
            var nro = table.rows('.selected').data().length;
        
            if (nro <= 0) {
                swal({
                    title: 'ERROR',
                    html: 'Para ejecutar esta acción, debe al menos seleccionar una factura electrónica',
                    type: 'error',
                });
                return false;
            }
        
            var facturas = [];
            for (i = 0; i < nro; i++) {
                facturas.push(table.rows('.selected').data()[i]['id']);
            }
        
            swal({
                title: '¿Desea realizar la emisión de ' + nro + ' facturas electrónicas?',
                text: 'Esto puede demorar unos minutos. Al Aceptar, no podrá cancelar el proceso',
                type: 'question',
                showCancelButton: true,
                confirmButtonColor: '#00ce68',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Aceptar',
                cancelButtonText: 'Cancelar',
            }).then((result) => {
                if (result.value) {
                    cargando(true);
        
                    var url = window.location.pathname.split("/")[1] === "software" ?
                        `/software/empresa/facturas/emisionmasivaxml/` + facturas :
                        `/empresa/facturas/emisionmasivaxml/` + facturas;
        
                    $.ajax({
                        url: url,
                        method: 'GET',
                        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                        success: function(data) {
                            cargando(false);
                            swal({
                                title: 'PROCESO REALIZADO',
                                html: data.text,
                                type: 'success',
                                showConfirmButton: true,
                                confirmButtonColor: '#1A59A1',
                                confirmButtonText: 'ACEPTAR',
                            });
                            getDataTable();
                        },
                        error: function(xhr) {
                            cargando(false);
                            if (xhr.status === 500) {
                                swal({
                                    title: 'INFO',
                                    html: 'Se han emitido algunas facturas, vuelve a emitir otro lote si quedan facturas pendientes.',
                                    type: 'info',
                                    showConfirmButton: true,
                                    confirmButtonColor: '#d33',
                                    confirmButtonText: 'Recargar Página',
                                }).then(() => {
                                    location.reload();
                                });
                            }
                        }
                    });
                }
            });
            console.log(facturas);
        });

		$('#btn_siigo').on('click', function(e) {
            var table = $('#tabla-facturas').DataTable();
            var nro = table.rows('.selected').data().length;

            if (nro <= 0) {
                swal({
                    title: 'ERROR',
                    html: 'Para ejecutar esta acción, debe al menos seleccionar una factura electrónica',
                    type: 'error',
                });
                return false;
            }

            var facturas = [];
            for (i = 0; i < nro; i++) {
                facturas.push(table.rows('.selected').data()[i]['id']);
            }

            swal({
                title: '¿Desea enviar ' + nro + ' facturas a Siigo?',
                text: 'Esto puede demorar unos minutos. Al Aceptar, no podrá cancelar el proceso',
                type: 'question',
                showCancelButton: true,
                confirmButtonColor: '#00ce68',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Aceptar',
                cancelButtonText: 'Cancelar',
            }).then((result) => {
                if (result.value) {
                    cargando(true);

                    var url = window.location.pathname.split("/")[1] === "software" ?
                        `/software/empresa/facturas/enviomasivosiigo/` + facturas :
                        `/empresa/facturas/enviomasivosiigo/` + facturas;
                    $.ajax({
                        url: url,
                        method: 'GET',
                        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                        success: function(data) {
                        cargando(false);

                        if (data.success == false) {
                            swal({
                                title: 'ERROR',
                                html: data.message,
                                type: 'error',
                                confirmButtonColor: '#d33',
                                confirmButtonText: 'ACEPTAR',
                            });
                            return false;
                        } else {
                            let html = '<ul>';
                            data.resultados.forEach(function(res) {
                                if (res.resultado.status === 200) {
                                    html += `<li style="color:green;">Factura ${res.codigo}: ${res.resultado.message}</li>`;
                                } else {
                                    html += `<li style="color:red;">Factura ${res.codigo}: ${res.resultado.error}</li>`;
                                }
                            });
                            html += '</ul>';

                            swal({
                                title: 'PROCESO REALIZADO',
                                html: html,
                                type: 'success',
                                confirmButtonColor: '#1A59A1',
                                confirmButtonText: 'ACEPTAR',
                            });
                        }
                        getDataTable();
                    },
                        error: function(xhr) {
                            cargando(false);
                            if (xhr.status === 500) {
                                swal({
                                    title: 'INFO',
                                    html: 'Se han enviado algunas facturas a siigo, vuelve a enviar otro lote.',
                                    type: 'info',
                                    showConfirmButton: true,
                                    confirmButtonColor: '#d33',
                                    confirmButtonText: 'Recargar Página',
                                }).then(() => {
                                    location.reload();
                                });
                            }
                        }
                    });
                }
            });
            console.log(facturas);
        });

		
		$('#btn_imp_fac').on('click', function(e) {
			var table = $('#tabla-facturas').DataTable();
			var nro = table.rows('.selected').data().length;

			if(nro <= 0){
				swal({
					title: 'ERROR',
					html: 'Para ejecutar esta acción, debe al menos seleccionar una factura.',
					type: 'error',
				});
				return false;
			}

			var facturas = [];
			for (i = 0; i < nro; i++) {
				facturas.push(table.rows('.selected').data()[i]['id']);
			}

			swal({
				title: '¿Desea imprimir '+nro+' facturas?',
				text: 'Esto puede demorar unos minutos. Al Aceptar, no podrá cancelar el proceso',
				type: 'question',
				showCancelButton: true,
				confirmButtonColor: '#00ce68',
				cancelButtonColor: '#d33',
				confirmButtonText: 'Aceptar',
				cancelButtonText: 'Cancelar',
			}).then((result) => {
				if (result.value) {
					cargando(true);

					const baseUrl = "{{ url('empresa/facturas/impresionmasiva') }}";
					const url = `${baseUrl}/${facturas.join(',')}`;
					window.open(url, '_blank');

					cargando(false);

					swal({
						title: 'PROCESO REALIZADO',
						html: 'Las facturas están siendo generadas en una nueva pestaña.',
						type: 'success',
						showConfirmButton: true,
						confirmButtonColor: '#1A59A1',
						confirmButtonText: 'ACEPTAR',
					});
				}
			})
		});
	});

	function getDataTable() {
		tabla.DataTable().ajax.reload();
	}

	function abrirFiltrador() {
		if ($('#form-filter').hasClass('d-none')) {
			$('#boton-filtrar').html('<i class="fas fa-times"></i> Cerrar');
			$('#form-filter').removeClass('d-none');
		} else {
			$('#boton-filtrar').html('<i class="fas fa-search"></i> Filtrar');
			cerrarFiltrador();
		}
	}

	function cerrarFiltrador() {
		$('#codigo').val('');
		$('#corte').val('').selectpicker('refresh');
		$('#cliente').val('').selectpicker('refresh');
		$('#municipio').val('').selectpicker('refresh');
		$('#vendedor').val('').selectpicker('refresh');
		$('#creacion').val('');
		$('#vencimiento').val('');
		$('#comparador').val('').selectpicker('refresh');
		$('#total').val('');
		$('#estado').val('').selectpicker('refresh');
		$('#servidor').val('').selectpicker('refresh');
		$('#emision').val('').selectpicker('refresh');
		$('#form-filter').addClass('d-none');
		$('#boton-filtrar').html('<i class="fas fa-search"></i> Filtrar');
		getDataTable();
	}

	function exportar() {
		$("#estado").selectpicker('refresh');
        window.location.href = window.location.pathname+'/exportar?codigo='+$('#codigo').val()+'&cliente='+$('#cliente').val()+'&municipio='+$('#municipio').val()+'&creacion='+$('#creacion').val()+'&vencimiento='+$('#vencimiento').val()+'&estado='+$('#estado').val()+'&tipo=2';
	}
</script> 
@endsection