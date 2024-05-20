@extends('layouts.app')
@section('content')
	<form method="POST" action="{{ route('plantillas.update', $plantilla->id) }}" style="padding: 2% 3%;" role="form" class="forms-sample" novalidate id="form-retencion" >
	    @csrf
	    <input name="_method" type="hidden" value="PATCH">
	    <div class="row">
	        <div class="col-md-6 form-group">
        	    <label class="control-label">Título <span class="text-danger">*</span></label>
        	    <input type="text" class="form-control"  id="title" name="title"  required="" value="{{$plantilla->title}}" maxlength="200">
        	    <span class="help-block error">
        	        <strong>{{ $errors->first('title') }}</strong>
        	    </span>
        	</div>
        	
	        <div class="col-md-3 form-group">
	            <label class="control-label">Tipo <span class="text-danger">*</span></label>
        	    <select name="tipo" id="tipo" class="form-control selectpicker " title="Seleccione" data-live-search="true" data-size="5" required>
        	        <option value="0" {{$plantilla->tipo==0?'selected':''}} >SMS</option>
        	        <option value="1" {{$plantilla->tipo==1?'selected':''}} >EMAIL</option>
        	        <option value="2" {{$plantilla->tipo==2?'selected':''}} >WHATSAPP</option>
        	    </select>
        	    <span class="help-block error">
        	        <strong>{{ $errors->first('tipo') }}</strong>
        	    </span>
        	</div>
        	
        	<div class="col-md-3 form-group">
	            <label class="control-label">Clasificación <span class="text-danger">*</span></label>
        	    <select name="clasificacion" id="clasificacion" class="form-control selectpicker " title="Seleccione" data-live-search="true" data-size="5" required>
        	        <option value="Bienvenida" {{$plantilla->clasificacion=='Bienvenida'?'selected':''}}>Bienvenida</option>
        	        <option value="Cobro" {{$plantilla->clasificacion=='Cobro'?'selected':''}}>Cobro</option>
        	        <option value="Notificacion" {{$plantilla->clasificacion=='Notificacion'?'selected':''}}>Notificación</option>
        	        <option value="Facturacion" {{$plantilla->clasificacion=='Facturacion'?'selected':''}}>Facturación</option>
        	    </select>
        	    <span class="help-block error">
        	        <strong>{{ $errors->first('clasificacion') }}</strong>
        	    </span>
        	</div>

        	<div class="col-md-12 form-group {{$plantilla->tipo==0?'d-none':''}}" id="div_variables">
        		<div class="alert alert-success" role="alert">
        			<h4 class="alert-heading">VARIABLLES DE PERSONALIZACIÓN</h4>
        			<p>Si al crear la plantilla, desea utilizar variables de personalización, le dejamos el listado de las variables disponibles. Por favor utilizarlas tal cual como se reflejan.</p>
        			<hr>
        			<div class="row mb-0">
        				<div class="col-md-4 form-group mb-0 offset-2">
        					<ul class="list-unstyled">
        						<li><strong>{{$name}}</strong> - Nombre del Cliente</li>
        						<li><strong>{{$date}}</strong> - Fecha</li>
        					</ul>
        				</div>
        				<div class="col-md-4 form-group mb-0">
        					<ul class="list-unstyled">
        						<li><strong>{{$company}}</strong> - Nombre de la Empresa</li>
        						<li><strong>{{$nit}}</strong> - Nit de la Empresa</li>
        					</ul>
        				</div>
        			</div>
        		</div>
        	</div>
        	
        	<div class="col-md-12 form-group {{$plantilla->tipo==0 || $plantilla->tipo==2?'d-none':''}}" id="div_email">
        	    <label class="control-label">Contenido <span class="text-danger">*</span></label>
        	    <textarea class="form-control ckeditor" name="contenido" id="contenido" rows="4">{{$plantilla->contenido}}</textarea>
        	    <span class="help-block error">
        	        <strong>{{ $errors->first('contenido') }}</strong>
        	    </span>
        	</div>

        	<div class="col-md-12 form-group {{$plantilla->tipo==1 || $plantilla->tipo==2?'d-none':''}}" id="div_sms">
        	    <label class="control-label">Contenido (Máximo 140 caracteres)<span class="text-danger">*</span></label>
        	    <textarea class="form-control" name="contenido_sms" id="contenido_sms" rows="2" maxlength="140">{{$plantilla->contenido}}</textarea>
        	    <span class="help-block error">
        	        <strong>{{ $errors->first('contenido_sms') }}</strong>
        	    </span>
        	</div>

        	<div class="col-md-12 form-group {{$plantilla->tipo==0 || $plantilla->tipo==1?'d-none':''}}" id="div_whatsapp">
        	    <label class="control-label">Contenido <span class="text-danger">*</span></label>
        	    <textarea class="form-control" name="contenido_whatsapp" id="contenido_whatsapp" rows="2">{{$plantilla->contenido}}</textarea>
        	    <span class="help-block error">
        	        <strong>{{ $errors->first('contenido_whatsapp') }}</strong>
        	    </span>
        	</div>
        </div>
	    
	   <small>Los campos marcados con <span class="text-danger">*</span> son obligatorios</small>
	   
	   <hr>
	   
	   <div class="row" >
	       <div class="col-sm-12" style="text-align: right;  padding-top: 1%;">
	           <a href="{{route('plantillas.index')}}" class="btn btn-outline-secondary">Cancelar</a>
	           <button type="submit" id="submitcheck" onclick="submitLimit(this.id)" class="btn btn-success">Guardar</button>
	       </div>
	   </div>
    </form>
@endsection

@section('scripts')
<script type="text/javascript">
    $('#tipo').change(function() {
    	if($("#tipo").val() == 0){
    		$("#div_email, #div_variables").addClass('d-none');
    		$("#div_sms").removeClass('d-none');
    	}else if($("#tipo").val() == 1){
    		$("#div_sms").addClass('d-none');
    		$("#div_email, #div_variables").removeClass('d-none');
    	}else if($("#tipo").val() == 2){
    		$("#div_email, #div_sms").addClass('d-none');
    		$("#div_whatsapp, #div_variables").removeClass('d-none');
    	}
    });
</script>
@endsection