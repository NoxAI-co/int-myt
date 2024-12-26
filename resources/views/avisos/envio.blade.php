@extends('layouts.app')
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

		<script type="text/javascript">
			setTimeout(function(){
			    $('.alert').hide();
			    $('.active_table').attr('class', ' ');
			}, 10000);
		</script>
	@endif
	<form method="POST" action="{{ route('avisos.envio_aviso') }}" style="padding: 2% 3%;" role="form" class="forms-sample" novalidate id="form-retencion">
	    @csrf
	    <input type="hidden" value="{{$opcion}}" name="type">
	    <div class="row">

			<div class="col-md-3 form-group">
				@if(!request()->vencimiento)
					<label>Facturas vencidas (opcional)</label>
					<input type="text" class="form-control datepicker"  id="vencimiento" value="" name="vencimiento">
				@else
				<a href="{{ url()->current() }}">
				<button type="button" class="btn btn-primary position-relative">
					Vencidas: {{ request()->vencimiento }}
					<span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
					  X
					</span>
				</button>
				</a>
				@endif
			</div>

	        <div class="col-md-3 form-group">
	            <label class="control-label">Plantilla <span class="text-danger">*</span></label>
        	    <select name="plantilla" id="plantilla" class="form-control selectpicker " title="Seleccione" data-live-search="true" data-size="5" required>
        	        @foreach($plantillas as $plantilla)
        	        <option {{old('plantilla')==$plantilla->id?'selected':''}} value="{{$plantilla->id}}">{{$plantilla->title}}</option>
        	        @endforeach
        	    </select>
        	    <span class="help-block error">
        	        <strong>{{ $errors->first('plantilla') }}</strong>
        	    </span>
        	</div>

			@if(isset($servidores))
			<div class="col-md-3 form-group">
	            <label class="control-label">Servidor<span class="text-danger"></span></label>
        	    <select name="servidor" id="servidor" class="form-control selectpicker " onchange="refreshClient()" title="Seleccione" data-live-search="true" data-size="5">
        	        @foreach($servidores as $servidor)
        	        <option {{old('servidor')==$servidor->id?'selected':''}} value="{{$servidor->id}}">{{$servidor->nombre}}</option>
        	        @endforeach
        	    </select>
        	    <span class="help-block error">
        	        <strong>{{ $errors->first('servidor') }}</strong>
        	    </span>
        	</div>
			@endif

			@if(isset($gruposCorte))
			<div class="col-md-3 form-group">
	            <label class="control-label">Grupo corte<span class="text-danger"></span></label>
        	    <select name="corte" id="corte" class="form-control selectpicker" onchange="refreshClient()" title="Seleccione" data-live-search="true" data-size="5">
        	        @foreach($gruposCorte as $corte)
        	        <option {{old('corte')==$corte->id?'selected':''}} value="{{$corte->id}}">{{$corte->nombre}}</option>
        	        @endforeach
        	    </select>
        	    <span class="help-block error">
        	        <strong>{{ $errors->first('corte') }}</strong>
        	    </span>
        	</div>
			@endif

        	<div class="col-md-3 form-group">
	            <label class="control-label">Barrio</label>
        	    <input class="form-control" type="text" name="barrio" id="barrio">
        	    <span class="help-block error">
        	        <strong>{{ $errors->first('barrio') }}</strong>
        	    </span>
        	</div>

            <div class="col-md-3 form-group">
	            <label class="control-label">ESTADO CLIENTE<span class="text-danger"></span></label>
        	    <select name="options" id="options" class="form-control selectpicker" onchange="chequeo()" title="Seleccione" data-live-search="true" data-size="5">
        	        <option {{old('options')==1?'selected':''}} value="1" id='radio_1'>HABILITADOS</option>
        	        <option {{old('options')==2?'selected':''}} value="2" id='radio_2'>DESHABILITADOS</option>
        	        <option {{old('options')==3?'selected':''}} value="3" id='radio_3'>MANUAL</option>
        	    </select>
        	    <span class="help-block error">
        	        <strong>{{ $errors->first('options') }}</strong>
        	    </span>
        	</div>

			<div class="col-md-3 form-group">
				<label class="control-label">OPCIONES SALDO<span class="text-danger"></span></label>
				<select name="opciones_saldo" id="opciones_saldo" class="form-control selectpicker" onchange="refreshClient()" title="Seleccione" data-live-search="true" data-size="5">
					<option {{old('opciones_saldo')=='mayor_a'?'selected':''}} value="mayor_a">SALDO MAYOR A</option>
					<option {{old('opciones_saldo')=='mayor_igual'?'selected':''}} value="mayor_igual">SALDO MAYOR O IGUAL A</option>
					<option {{old('opciones_saldo')=='igual_a'?'selected':''}} value="igual_a">SALDO IGUAL A</option>
					<option {{old('opciones_saldo')=='menor_a'?'selected':''}} value="menor_a">SALDO MENOR A</option>
					<option {{old('opciones_saldo')=='menor_igual'?'selected':''}} value="menor_igual">SALDO MENOR IGUAL A</option>
				</select>
				<span class="help-block error">
        	        <strong>{{ $errors->first('options') }}</strong>
        	    </span>
			</div>

			<div class="col-md-3 form-group">
				<label class="control-label">Valor Saldo</label>
				<input class="form-control" type="text" name="valor_saldo" id="valor_saldo"  oninput="refreshClient()">
				<span class="help-block error">
        	        <strong>{{ $errors->first('barrio') }}</strong>
        	    </span>
			</div>

			<div class="col-md-3 form-group">
				<label class="control-label">PLANES</label>
				<select name="item" id="item" class="form-control selectpicker" onchange="refreshClient()" title="Seleccione" data-live-search="true" data-size="5">
					@foreach($items as $item)
					<option {{old('item')==$item->id?'selected':''}} value="{{ $item->id }}">{{ $item->producto }}</option>
					@endforeach
				</select>
				<span class="help-block error">
        	        <strong>{{ $errors->first('item') }}</strong>
        	    </span>
			</div>

        	<div class="col-md-3 form-group" id="seleccion_manual">
	            <label class="control-label">Selección manual de clientes</label>
        	    <select name="contrato[]" id="contrato_sms" class="form-control selectpicker" title="Seleccione" data-live-search="true" data-size="5" required multiple data-actions-box="true" data-select-all-text="Todos" data-deselect-all-text="Ninguno">
        	        @php $estados=\App\Contrato::tipos();@endphp
        	        @foreach($estados as $estado)
        	        <optgroup label="{{$estado['nombre']}}">
        	            @foreach($contratos as $contrato)
        	                @if($contrato->state==$estado['state'])
        	                    <option class="{{$contrato->state}} 
									servidor-{{ $contrato->servidor()->id ?? 'no' }}
									factura-{{ $contrato->factura_id != null ?  'si' : 'no'}}
									{{$contrato->plan_id != null ? "plan-" . $contrato->plan_id : 'plan-no'}}
									"
									value="{{$contrato->id}}" {{$contrato->client_id==$id?'selected':''}}
										data-saldo="<?php echo e($contrato->factura_total); ?>"
										>

									{{$contrato->c_nombre}} {{ $contrato->c_apellido1 }}
									{{ $contrato->c_apellido2 }} - {{$contrato->c_nit}}
									(contrato: {{ $contrato->nro }}) {{$contrato->plan_id != null ? "plan-" . $contrato->plan_id : 'plan-no' }}
								</option>
        	                
        	                @endif
        	            @endforeach
        	        </optgroup>
        	        @endforeach
        	    </select>
        	    <span class="help-block error">
        	        <strong>{{ $errors->first('cliente') }}</strong>
        	    </span>
        	</div>

			<div class="col-md-3">
				<div class="form-check form-check-inline d-flex p-3">
					<input class="form-check-input" type="checkbox" id="isAbierta" name="isAbierta" value="true" onclick="refreshClient()">
					<label class="form-check-label" for="isAbierta"  style="font-weight:bold">Solo facturas abiertas</label>
				</div>
			</div>

       </div>

	   <small>Los campos marcados con <span class="text-danger">*</span> son obligatorios</small>

	   <hr>

	   <div class="row" >
	       <div class="col-sm-12" style="text-align: right;  padding-top: 1%;">
	           <a href="{{route('avisos.index')}}" class="btn btn-outline-secondary">Cancelar</a>
	           <button type="submit" id="submitcheck" onclick="submitLimit(this.id); alert_swal();" class="btn btn-success">Guardar</button>
	       </div>
	   </div>
    </form>
@endsection

@section('scripts')
<script type="text/javascript">

	var ultimoVencimiento = null;


	window.addEventListener('load', function() {

		$('#vencimiento').on('change', function(){
			if($(this).val() == ultimoVencimiento){

			}else{
				ultimoVencimiento = $(this).val();
				window.location.href =  window.location.pathname + '?' + 'vencimiento=' + ultimoVencimiento;
			}
		});


		$('#barrio').on('keyup',function(e) {
        	if(e.which > 32 || e.which == 8) {
        		if($('#barrio').val().length > 3){
        			if (window.location.pathname.split("/")[1] === "software") {
        				var url = '/software/getContractsBarrio/'+$('#barrio').val();
        			}else{
        				var url = '/getContractsBarrio/'+$('#barrio').val();
        			}

        			cargando(true);

        			$.ajax({
        				url: url,
        				headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
        				method: 'get',
        				success: function (data) {
        					console.log(data);
        					cargando(false);

        					var $select = $('#contrato_sms');
        					$select.empty();
        					$.each(data.data,function(key, value){
        						var apellidos = '';
        						if(value.apellido1){
        							apellidos += ' '+value.apellido1;
        						}
        						if(value.apellido2){
        							apellidos += ' '+value.apellido2;
        						}
        						$select.append('<option value='+value.id+' class="'+value.state+'">'+value.nombre+' '+apellidos+' - '+value.nit+'</option>');
        					});
        					$select.selectpicker('refresh');
							refreshClient();
        				},
        				error: function(data){
        					cargando(false);
        				}
        			});
        		}
        		return false;
        	}
        });
    });

    function chequeo(){
        if($("#radio_1").is(":selected")){
            $(".enabled").attr('selected','selected');
            $(".disabled").removeAttr("selected");

			refreshClient('enabled',1);
        }else if($("#radio_2").is(":selected")){
            $(".disabled").attr('selected','selected');
            $(".enabled").removeAttr("selected");

			refreshClient('disabled',1);
        }else if($("#radio_3").is(":selected")){

        }
        $("#contrato").selectpicker('refresh');
    }

    function alert_swal(){
    	Swal.fire({
    		type: 'info',
    		title: 'ENVIANDO NOTIFICACIONES',
    		text: 'Este proceso puede demorar varios minutos',
    		showConfirmButton: false,
    	})
    }

	function refreshClient(estadoCliente = null, disabledEstado = null){

		let grupoCorte = $('#corte').val();
		let servidor = $('#servidor').val();
		let factAbierta = $('#isAbierta').is(":checked");
		let tipoSaldo = $('#opciones_saldo').val();
		let valorSaldo = parseFloat($('#valor_saldo').val());
		let plan = $('#item').val();

		// let options = '';

		if(estadoCliente){
			
			if(grupoCorte && servidor){
				options = $(`.servidor-${servidor}.grupo-${grupoCorte}.${estadoCliente}`);
			}else{
				if(servidor){
					options = $(`.servidor-${servidor}.${estadoCliente}`);
				}
				if(grupoCorte){
					options = $(`.grupo-${servidor}.${estadoCliente}`);
				}
				if(plan){
					options = $(`.plan-${plan}`);
				}
			}

			if(factAbierta && grupoCorte && servidor){
			options=$(`.servidor-${servidor}.grupo-${grupoCorte}.${estadoCliente}.factura-si`);
			}else if(factAbierta && grupoCorte){
				options=$(`.grupo-${grupoCorte}.${estadoCliente}.factura-si`);
			}else if(factAbierta && servidor){
				options=$(`.servidor-${servidor}.${estadoCliente}.factura-si`);
			}else if(factAbierta){
				options=`${estadoCliente}.factura-si`;
			}

		}else{

			if(grupoCorte && servidor){
				options = $(`.servidor-${servidor}.grupo-${grupoCorte}`);
			}else{
				if(servidor){
					options = $(`#contrato_sms option[class*="servidor-${servidor}"]`);
				}
				if(grupoCorte){
					 options = $(`#contrato_sms option[class*="grupo-${grupoCorte}"]`);
				}
				if(plan){
					options = $(`.plan-${plan}`);
				}
			}

			if(factAbierta && grupoCorte && servidor){
			options=$(`.servidor-${servidor}.grupo-${grupoCorte}.factura-si`);
			}else if(factAbierta && grupoCorte){
				options=$(`.grupo-${grupoCorte}.factura-si`);
			}else if(factAbierta && servidor){
				options=$(`.servidor-${servidor}.factura-si`);
			}else if(factAbierta){
				options=`.factura-si`;
			}
		}

		// Filtrar por valor de factura si se ingresa un valor en el input
		// Si el tipo de saldo y el valor están definidos
		if (tipoSaldo && !isNaN(valorSaldo) && options) {
			options = options.filter(function() {
				let saldo = parseFloat($(this).data('saldo'));
				switch (tipoSaldo) {
					case 'mayor_a':
						return saldo > valorSaldo;
					case 'mayor_igual':
						return saldo >= valorSaldo;
					case 'igual_a':
						return saldo === valorSaldo;
					case 'menor_a':
						return saldo > 0 && saldo < valorSaldo;
					case 'menor_igual':
						return saldo > 0 && saldo <= valorSaldo;
					default:
						return true;
				}
			});
		}
		console.log(options);
		// Si se encontraron opciones válidas, seleccionarlas
		if (options) {

			if ((grupoCorte || servidor) && disabledEstado == null) {
				$("#options option:selected").prop("selected", false);
				$("#options").selectpicker('refresh');
			}

			$("#contrato_sms option:selected").prop("selected", false);
			$("#contrato_sms option:selected").removeAttr("selected");

			options.attr('selected', true);
			options.prop('selected', true);

			$('#contrato_sms').selectpicker('refresh');
		}
	}

</script>
@endsection
