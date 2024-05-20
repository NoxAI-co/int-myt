@extends('layouts.app')

@section('content')
    @if(Session::has('success-newtypecontact'))
        <div class="alert alert-success" >
        	{{Session::get('success-newtypecontact')}}
        </div>
        <script type="text/javascript">
        	setTimeout(function(){
        		$('.alert').hide();
        		$('.active_table').attr('class', ' ');
        	}, 5000);
        </script>
    @endif

    <form method="POST" action="{{ route('ventas-externas.store') }}" style="padding: 2% 3%;" role="form" class="forms-sample" id="form-contacto">
    	@csrf
    	<input type="hidden" name="tipo_contacto" id="tipo_contacto" value="0">
    	<div class="row">
    		<div class="form-group col-md-3">
    			<label class="control-label">Tipo de Identificación <span class="text-danger">*</span></label>
    			<select class="form-control selectpicker" name="tip_iden" id="tip_iden" required="" onchange="searchDV(this.value)" title="Seleccione">
    				@foreach($identificaciones as $identificacion)
    				<option @if($identificacion->id == 3) ? selected @endif {{old('tip_iden')==$identificacion->id?'selected':''}} value="{{$identificacion->id}}" title="{{$identificacion->mini()}}">{{$identificacion->identificacion}}</option>
    				@endforeach
    			</select>
    			<span class="help-block error">
    				<strong>{{ $errors->first('tip_iden') }}</strong>
    			</span>
    		</div>
    		<div class="form-group col-md-3">
    			<label class="control-label">Identificación <span class="text-danger">*</span><a><i data-tippy-content="Identificación de la persona" class="icono far fa-question-circle"></i></a></label>
    			<input type="text" class="form-control" name="nit" id="nit" required="" maxlength="20" value="{{old('nit')}}" onkeypress="return event.charCode >= 48 && event.charCode <=57">
    			<span class="help-block error">
    				<strong>{{ $errors->first('nit') }}</strong>
    			</span>
    		</div>
    		<div class="form-group col-md-1" style="display: none;" id="dvnit">
    			<label class="control-label">DV <span class="text-danger">*</span></label>
    			<input type="text" class="form-control" name="dv" id="dv" disabled required="" maxlength="20" value="" onkeypress="return event.charCode >= 48 && event.charCode <=57">
    			<input type="hidden" name="dvoriginal" id="dvoriginal" value="">
    			<span class="help-block error">
    				<strong>{{ $errors->first('dv') }}</strong>
    			</span>
    		</div>
    		<div class="form-group col-md-3">
    			<label class="control-label">Nombres <span class="text-danger">*</span></label>
    			<input type="text" class="form-control" name="nombre" id="nombre" required="" maxlength="200" value="{{old('nombre')}}">
    			<span class="help-block error">
    				<strong>{{ $errors->first('nombre') }}</strong>
    			</span>
    		</div>
    		<div class="form-group col-md-3">
    			<label class="control-label">Apellido 1 <span class="text-danger">*</span></label>
    			<input type="text" class="form-control" name="apellido1" id="apellido1" required="" maxlength="200" value="{{old('apellido1')}}">
    			<span class="help-block error">
    				<strong>{{ $errors->first('apellido1') }}</strong>
    			</span>
    		</div>
    		<div class="form-group col-md-3">
    			<label class="control-label">Apellido 2</label>
    			<input type="text" class="form-control" name="apellido2" id="apellido2" maxlength="200" value="{{old('apellido2')}}">
    			<span class="help-block error">
    				<strong>{{ $errors->first('apellido2') }}</strong>
    			</span>
    		</div>
    	</div>

	    <div class="row">
	  		<div class="form-group col-md-3">
	  			<label class="control-label">País</label>
	  			<select class="form-control   selectpicker" name="pais" id="pais" required="" title="Seleccione" data-live-search="true" data-size="5" onchange="validateCountry(this.value)">
	  				@foreach($paises as $pais)
	  				  <option value="{{$pais->codigo}}" {{ $pais->codigo == 'CO' ? 'selected' : '' }}>{{$pais->nombre}}</option>
	  				@endforeach
	  			</select>
	  		</div>
	  		<div class="form-group col-md-3" id="validatec1">
	  			<label class="control-label">Departamento <span class="text-danger">*</span></label>
	  			<select class="form-control selectpicker" name="departamento" id="departamento" required="" title="Seleccione" data-live-search="true" data-size="5" onchange="searchMunicipality(this.value, {{ Auth::user()->empresa()->fk_idmunicipio }})">
	  				@foreach($departamentos as $departamento)
	  				  <option value="{{ $departamento->id }}">{{ $departamento->nombre }}</option>
	  				@endforeach
	  			</select>
	  		</div>
	  		<div class="form-group col-md-3" id="validatec2">
	  			<label class="control-label">Municipio <span class="text-danger">*</span></label>
	  			<select class="form-control selectpicker" name="municipio" id="municipio" required="" title="Seleccione" data-live-search="true" data-size="5">

	  			</select>
	  		</div>
	  		<div class="form-group col-md-3" id="validatec3">
	  			<label class="control-label">Código Postal</label>
	  			<a><i data-tippy-content="Si desconoces tu código postal <a target='_blank' href='http://visor.codigopostal.gov.co/472/visor/'>haz click aquí</a>" class="icono far fa-question-circle"></i></a>
	  			<input type="text" class="form-control" id="cod_postal" name="cod_postal" maxlength="200"  value="{{old('cod_postal')}}" onkeypress="return event.charCode >= 48 && event.charCode <=57">
	  		</div>

	  		<div class="form-group col-md-6">
	  			<label class="control-label">Dirección <span class="text-danger">*</span></label>
	  			<input type="text" name="direccion" class="form-control" value="{{old('direccion')}}" required>
	  			<span class="help-block error">
	  				<strong>{{ $errors->first('direccion') }}</strong>
	  			</span>
	  		</div>
              <div class="form-group col-md-3">
                <label class="control-label">Corregimiento/Vereda</label>
                <input type="text" name="vereda" class="form-control" value="{{old('vereda')}}">
                <span class="help-block error">
                    <strong>{{ $errors->first('vereda') }}</strong>
                </span>
            </div>
	  		<div class="form-group col-md-3">
	  			<label class="control-label">Barrio </label>
	  			<input type="text" name="barrio" class="form-control" value="{{old('barrio')}}">
	  			<span class="help-block error">
	  				<strong>{{ $errors->first('barrio') }}</strong>
	  			</span>
	  		</div>
	  		<div class="form-group col-md-3">
	  			<label class="control-label" for="email">Correo Electrónico <span class="text-danger">*</span></label>
	  			<input type="email" class="form-control" id="email" name="email" data-error="Dirección de correo electrónico invalida" maxlength="100"  value="{{old('email')}}" required>
	  			<div class="help-block error with-errors"></div>
	  			<span class="help-block error">
	  				<strong>{{ $errors->first('email') }}</strong>
	  			</span>
	  		</div>
	  	</div>

	  	<div class="row">
	  		<div class="form-group col-md-3">
	  			<label class="control-label">Teléfono</label>
	  			<input type="text" class="form-control" id="telefono1" name="telefono1" maxlength="15" value="{{old('telefono1')}}" onkeypress="return event.charCode >= 48 && event.charCode <=57">
	  			<span class="help-block error">
	  				<strong>{{ $errors->first('telefono1') }}</strong>
	  			</span>
	  		</div>
	  		<div class="form-group col-md-3">
	  			<label class="control-label">Celular <span class="text-danger">*</span></label>
	  			<input type="text" class="form-control" id="celular" name="celular" maxlength="15" value="{{old('celular')}}" onkeypress="return event.charCode >= 48 && event.charCode <=57" required>
	  			<span class="help-block error">
	  				<strong>{{ $errors->first('celular') }}</strong>
	  			</span>
	  		</div>
	  		<div class="form-group col-md-3">
	  			<label class="control-label">Teléfono 2</label>
	  			<input type="text" class="form-control" id="telefono2" name="telefono2" maxlength="15" value="{{old('telefono2')}}" onkeypress="return event.charCode >= 48 && event.charCode <=57">
	  			<span class="help-block error">
	  				<strong>{{ $errors->first('telefono2') }}</strong>
	  			</span>
	  		</div>
	  		<div class="form-group col-md-3">
	  			<label class="control-label">Fax</label>
	  			<input type="text" class="form-control" id="fax" name="fax" maxlength="50" value="{{old('fax')}}" onkeypress="return event.charCode >= 48 && event.charCode <=57">
	  			<span class="help-block error">
	  				<strong>{{ $errors->first('fax') }}</strong>
	  			</span>
	  		</div>

              <div class="form-group col-md-3">
                <label class="control-label">Monitoreo</label>
                <input type="text" class="form-control" id="monitoreo" name="monitoreo" maxlength="50" value="{{old('monitoreo')}}" >
                <span class="help-block error">
                    <strong>{{ $errors->first('monitoreo') }}</strong>
                </span>
            </div>
            <div class="form-group col-md-3">
                <label class="control-label">Refiere</label>
                <input type="text" class="form-control" id="refiere" name="refiere" maxlength="50" value="{{old('refiere')}}" >
                <span class="help-block error">
                    <strong>{{ $errors->first('refiere') }}</strong>
                </span>
            </div>
            <div class="form-group col-md-3">
                <label class="control-label">Combo INT y TV</label>
                <input type="text" class="form-control" id="combo_int_tv" name="combo_int_tv" maxlength="50" value="{{old('combo_int_tv')}}" >
                <span class="help-block error">
                    <strong>{{ $errors->first('combo_int_tv') }}</strong>
                </span>
            </div>
            <div class="form-group col-md-3">
                <label class="control-label">Referencia I</label>
                <input type="text" class="form-control" id="referencia_1" name="referencia_1" maxlength="50" value="{{old('referencia_1')}}" >
                <span class="help-block error">
                    <strong>{{ $errors->first('referencia_1') }}</strong>
                </span>
            </div>
            <div class="form-group col-md-3">
                <label class="control-label">Referencia II</label>
                <input type="text" class="form-control" id="referencia_2" name="referencia_2" maxlength="50" value="{{old('referencia_2')}}" >
                <span class="help-block error">
                    <strong>{{ $errors->first('referencia_2') }}</strong>
                </span>
            </div>
            <div class="form-group col-md-3">
                <label class="control-label">Cierra Venta</label>
                <input type="text" class="form-control" id="cierra_venta" name="cierra_venta" maxlength="50" value="{{old('cierra_venta')}}" >
                <span class="help-block error">
                    <strong>{{ $errors->first('cierra_venta') }}</strong>
                </span>
            </div>
	  	</div>

	  	<div class="row">
            <div class="form-group col-md-3">
                <label class="control-label">Planes</label>
                <select class="form-control  selectpicker" name="plan" id="plan" required="" title="Seleccione" data-live-search="true" data-size="5" onchange="validateCountry(this.value)">
                    @foreach($planes as $plan)
                      <option value="{{$plan->id}}">{{$plan->name}}</option>
                    @endforeach
                </select>
            </div>

            <div class="form-group col-md-3">
                <label class="control-label">Costo Instalación</label>
                <input type="text" class="form-control" id="costo_instalacion" name="costo_instalacion" maxlength="50" value="{{old('costo_instalacion')}}" >
                <span class="help-block error">
                    <strong>{{ $errors->first('costo_instalacion') }}</strong>
                </span>
            </div>
	  		<div class="form-group col-md-3">
	  			<label class="control-label">Estrato</label>
	  			<select class="form-control selectpicker" id="estrato" name="estrato" title="Seleccione" data-live-search="true" data-size="5">
	  				<option value="1">1</option>
	  				<option value="2">2</option>
	  				<option value="3">3</option>
	  				<option value="4">4</option>
	  				<option value="5">5</option>
	  				<option value="6">6</option>
	  			</select>
	  			<span class="help-block error">
	  				<strong>{{ $errors->first('estato') }}</strong>
	  			</span>
	  		</div>
	        <div class="col-md-3 form-group">
	            <label class="control-label">Vendedor <a><i data-tippy-content="Seleccione el vendedor del contrato. Para agregar otros vendedores ingrese al <a href='{{ route('vendedores.index') }}' target='_blank'>módulo de vendedores</a>" class="icono far fa-question-circle"></i></a></label>
	            <div class="input-group mb-2">
	                <select class="form-control selectpicker" name="vendedor" id="vendedor" title="Seleccione" data-live-search="true" data-size="5" required>
	                    @foreach($vendedores as $vendedor)
	                    <option value="{{$vendedor->id}}" {{old('vendedor')==$vendedor->id?'selected':''}}>{{$vendedor->nombre}}</option>
	                    @endforeach
	                </select>
	                <span style="color: red;">
	                    <strong>{{ $errors->first('vendedor') }}</strong>
	                </span>
	            </div>
	        </div>
	        <div class="col-md-3 form-group">
	            <label class="control-label">Canal de Ventas <a><i data-tippy-content="Seleccione el canal de venta. Para agregar otros vendedores ingrese al <a href='{{ route('canales.index') }}' target='_blank'>módulo de canales de venta</a>" class="icono far fa-question-circle"></i></a></label>
	            <div class="input-group mb-2">
	                <select class="form-control selectpicker" name="canal" id="canal" title="Seleccione" data-live-search="true" data-size="5" required>
	                    @foreach($canales as $canal)
	                    <option value="{{$canal->id}}" {{old('canal')==$canal->id?'selected':''}}>{{$canal->nombre}}</option>
	                    @endforeach
	                </select>
	                <span style="color: red;">
	                    <strong>{{ $errors->first('canal') }}</strong>
	                </span>
	            </div>
	        </div>
	        @if(Auth::user()->empresa()->oficina)
	        <div class="form-group col-md-3">
	        	<label class="control-label">Oficina Asociada <span class="text-danger">*</span></label>
	        	<select class="form-control selectpicker" name="oficina" id="oficina" required="" title="Seleccione" data-live-search="true" data-size="5">
	        		@foreach($oficinas as $oficina)
	        		<option value="{{$oficina->id}}" {{ $oficina->id == auth()->user()->oficina ? 'selected' : '' }}>{{$oficina->nombre}}</option>
	        		@endforeach
	        	</select>
	        </div>
	        @endif
	  	    <div class="form-group col-md-3 d-none">
				<label class="control-label">Tipo de Contacto <span class="text-danger">*</span></label>
				<div class="form-check form-check-flat">
					<label class="form-check-label">
						<input type="checkbox" class="form-check-input" name="contacto[]" value="0" checked=""> Cliente
						<i class="input-helper"></i></label>
				</div>
				<div class="form-check form-check-flat">
					<label class="form-check-label">
						<input type="checkbox" class="form-check-input" name="contacto[]" value="1"> Proveedor
						<i class="input-helper"></i></label>
				</div>
				<span class="help-block error">
					<strong>{{ $errors->first('contacto') }}</strong>
				</span>
			</div>
	  		<div class="form-group col-md-6">
	  			<label class="control-label">Observaciones</label>
	  			<textarea class="form-control" name="observaciones" rows="10" cols="50" >{{old('observaciones')}}</textarea>
	  			<span class="help-block error">
	  				<strong>{{ $errors->first('observaciones') }}</strong>
	  			</span>
	  		</div>
	  	</div>

	  	<small>Los campos marcados con <span class="text-danger">*</span> son obligatorios</small>

	  	<hr>

	  	<div class="row" style="text-align: right;">
	  		<div class="col-md-12">
	  			<a href="{{route('ventas-externas.index')}}" class="btn btn-outline-light" >Cancelar</a>
	  			<button type="submit" class="btn btn-success">Guardar</button>
	  		</div>
	  	</div>
	</form>

	<script src="https://code.jquery.com/jquery-3.3.1.min.js" integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8=" crossorigin="anonymous"></script>

	<script>
		$(document).ready(function(){
			let lastRegis = new URLSearchParams(window.location.search);
			if(lastRegis.has('cnt')){
				let idCnt     = lastRegis.get('cnt');
				setTimeout(function () {
					$('#tipo_empresa').val(idCnt).change();
					clearTimeout(this);
				}, 1000);
			}
		});
	</script>
@endsection

@section('scripts')
	<script type="text/javascript">
		$(document).ready(function(){
			$('#departamento').val({{ Auth::user()->empresa()->fk_iddepartamento }}).selectpicker('refresh');
			var option = document.getElementById('tip_iden').value;

			if (option == 6) {
				searchDV($("#tip_iden").val());
			}
			searchMunicipality({{ Auth::user()->empresa()->fk_iddepartamento }}, {{ Auth::user()->empresa()->fk_idmunicipio }});
		});

		setTimeout(function () {
			$("#municipio").val({{ Auth::user()->empresa()->fk_idmunicipio }});
			$("#municipio").selectpicker('refresh');
    }, 500);
	</script>
@endsection
