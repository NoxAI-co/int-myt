@extends('layouts.app')
@section('content')
<input type="hidden" id="valuefecha" value="{{$request->fechas}}">
<input type="hidden" id="primera" value="{{$request->date ? $request->date['primera'] : ''}}">
<input type="hidden" id="ultima" value="{{$request->date ? $request->date['ultima'] : ''}}">

@if(Session::has('success'))
<div class="alert alert-success">
    {{Session::get('success')}}
</div>
<script type="text/javascript">
    setTimeout(function() {
        $('.alert-success').hide();
        $('.alert-danger').hide();
        $('.active_table').attr('class', ' ');
    }, 20000);
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
    }, 20000);
</script>
@endif

	<form id="form-reporte">
	<div class="row card-description">

	  	<div class="form-group col-md-4">
			<div class="row">
				<div class="col-md-12">
					<label>Fecha Factura<span class="text-danger">*</span></label>
                    @if(isset($empresa->cron_fecha_whatsapp) && $empresa->cron_fecha_whatsapp != null)
                    <input type="text" class="form-control" id="fecha" value="{{date('d-m-Y', strtotime($empresa->cron_fecha_whatsapp))}}" name="fecha" required="" >
                    @else
					<input type="text" class="form-control"  id="fecha" value="{{$request->fecha}}" name="fecha" required="" >
                    @endif
				</div>
			</div>
	  	</div>

	  	<div class="form-group col-md-8" style=" padding-top: 24px;">
        	<button type="button" id="generar" class="btn btn-outline-primary">Guardar Configuración</button>
            <button type="button" id="enviar-lote" class="btn btn-outline-info">Enviar lote de 45</button>
            <button type="button" id="reiniciar-lote" class="btn btn-outline-danger">Reiniciar envío de facturas</button>
	  	</div>
	</div>

    <div class="card-description w-full pt-0 m-0">
            <div class="alert alert-info p-2 m-0" role="alert">
                <strong>Nota:</strong><br>
                <strong>></strong> reporte muestra las facturas que no han sido enviadas por WhatsApp creadas en la fecha seleccionada. <br>
                <strong>></strong> Las facturas se envían en lotes de 45 cada 15 minutos. <br>
                <strong>></strong> Si dejas configurada una fecha diferente al dia de hoy, el siguiente dia se reestablece la fecha al dia actual. <br>
                @if($totalFaltantes == 0)
                <strong>></strong> No hay facturas con fecha <strong> {{ $request->fecha }}</strong> pendientes de envío.<br>
                @else
                <strong>></strong> <strong>{{ $totalFaltantes }}</strong> facturas no se han enviado por whatsapp.<br>
                @endif
                @if($sinTelefono > 0)
                <strong>></strong> Hay un total de <strong> {{ $sinTelefono }}</strong> facturas sin número celular registrado.<br>
                @endif
                @if(Auth::user()->empresa()->cron_fecha_whatsapp != null)
                <strong>></strong> La fecha de envío configurada actualmente es: <strong>{{ date('d-m-Y', strtotime(Auth::user()->empresa()->cron_fecha_whatsapp)) }}</strong>
                @endif

        </div>
    </div>

    <input type="hidden" name="orderby"id="order_by"  value="2">
    <input type="hidden" name="order" id="order" value="1">
    <input type="hidden" id="form" value="form-reporte">

	<div class="row card-description">
		<div class="col-md-12 table-responsive">
			<table class="table table-striped table-hover " id="table-facturas">
			<thead class="thead-dark">
				<tr>
                    <th>Nro. Factura</th>
                    <th>¿Enviado?</th>
                    <th>Cliente <button type="button" class="btn btn-link no-padding orderby {{$request->orderby==1?'':'no_order'}}" campo="1" order="@if($request->orderby==1){{$request->order==1?'0':'1'}}@else 0 @endif" ><i class="fas fa-arrow-@if($request->orderby==1){{$request->order==0?'up':'down'}}@else{{'down'}} @endif"></i></button> </th>
					<th>Grupo Corte <button type="button" class="btn btn-link no-padding orderby {{$request->orderby==2?'':'no_order'}}" campo="2" order="@if($request->orderby==2){{$request->order==1?'0':'1'}}@else 0 @endif" ><i class="fas fa-arrow-@if($request->orderby==2){{$request->order==0?'up':'down'}}@else{{'down'}} @endif"></i></button></th>
					<th>Servidor <button type="button" class="btn btn-link no-padding orderby {{$request->orderby==3?'':'no_order'}}" campo="3" order="@if($request->orderby==3){{$request->order==1?'0':'1'}}@else 0 @endif" ><i class="fas fa-arrow-@if($request->orderby==3){{$request->order==0?'up':'down'}}@else{{'down'}} @endif"></i></button></th>
                    <th>Creación <button type="button" class="btn btn-link no-padding orderby {{$request->orderby==4?'':'no_order'}}" campo="4" order="@if($request->orderby==4){{$request->order==1?'0':'1'}}@else 0 @endif" ><i class="fas fa-arrow-@if($request->orderby==4){{$request->order==0?'up':'down'}}@else{{'down'}} @endif"></i></button></th>
                    <th>Vencimiento <button type="button" class="btn btn-link no-padding orderby {{$request->orderby==5?'':'no_order'}}" campo="5" order="@if($request->orderby==5){{$request->order==1?'0':'1'}}@else 0 @endif" ><i class="fas fa-arrow-@if($request->orderby==5){{$request->order==0?'up':'down'}}@else{{'down'}} @endif"></i></button></th>
	          </tr>
			</thead>
			<tbody>

				@foreach($facturas as $factura)
					<tr>
                        <td><a href="{{route('facturas.show',$factura->id)}}" target="_blank">{{$factura->codigo}}</a> </td>
                        <td>{{$factura->whatsapp == 1 ? 'Si' : 'No'}}</td>
                        <td><a href="{{route('contactos.show',$factura->cliente()->id)}}" target="_blank">{{$factura->cliente()->nombre}}  {{$factura->cliente()->apellidos()}} @if($factura->cliente()->celular) | {{$factura->cliente()->celular}}@endif</a></td>
						<td>{{$factura->grupoNombre ?? ''}}</td>
						<td>{{$factura->servidor()->nombre ?? ''}}</td>
                        <td>{{date('d-m-Y', strtotime($factura->fecha))}}</td>
                        <td>{{date('d-m-Y', strtotime($factura->vencimiento))}}</td>
					</tr>
				@endforeach
			</tbody>
		</table>
            {!! $facturas->render() !!}
	</div>
</div>
</form>
<input type="hidden" id="urlgenerar" value="{{route('cronjob.whatsapp-facturas-save')}}">
<input type="hidden" id="url-enviar-lote" value="{{route('cronjob.whatsapp-facturas-envio')}}">
<input type="hidden" id="url-reiniciar-lote" value="{{route('cronjob.whatsapp-facturas-reiniciar')}}">
@endsection

@section('scripts')
<script>
    $('#enviar-lote').on('click', function(e) {
        e.preventDefault();

        let url = $('#url-enviar-lote').val();
        $('#enviar-lote').prop('disabled', true).text('Enviando...');

        // Iniciar temporizador de 90 segundos
        const timeout = setTimeout(function() {
            // alert('La operación tardó demasiado. Recargando la página...');
            location.reload();
        }, 90000); // 90,000 milisegundos = 1 minuto 30 segundos

        $.ajax({
            url: url,
            type: 'GET',
            success: function(response) {
                clearTimeout(timeout); // Detener el timeout si responde a tiempo
                alert(response.message || 'Los mensajes se enviaron correctamente.');
            },
            error: function(xhr, status, error) {
                clearTimeout(timeout); // Detener el timeout si hay error
                console.error(xhr.responseText);
                alert('Ocurrió un error al enviar los mensajes.');
            },
            complete: function() {
                $('#enviar-lote').prop('disabled', false).text('Enviar WhatsApp');
            }
        });
    });


    $('#reiniciar-lote').on('click', function() {
        Swal.fire({
        title: "¿Reiniciar envío de facturas?",
        text: "Las facturas ya enviadas el día " + $("#fecha").val() + " quedarán en estado 'No Enviadas'. (esto se hace con el fin de enviar de nuevo todas las facturas de una fecha especifica)",
        type: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        cancelButtonText: 'Cancelar',
        confirmButtonText: 'Reiniciar',
    }).then((result) => {
        if (result.value) {
            $('#form-reporte').attr('action', $("#url-reiniciar-lote").val());
            $('#form-reporte').submit();
        }
    })
    });
</script>
@endsection