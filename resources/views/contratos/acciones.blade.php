<form action="{{ route('contratos.destroy',$id) }}" method="post" class="delete_form" style="margin:  0;display: inline-block;" id="eliminar-contrato{{$id}}">
    @csrf
    <input name="_method" type="hidden" value="DELETE">
</form>
<form action="{{ route('contratos.state',$id) }}" method="post" class="delete_form" style="margin:0;display: inline-block;" id="cambiar-state{{$id}}">
    @csrf
</form>

<form action="{{ route('contratos.state_oltcatv',$id) }}" method="post" class="delete_form" style="margin:0;display: inline-block;" id="cambiar-statecatv{{$id}}">
    @csrf
</form>

@if(Auth::user()->rol == 3 && $mk == 0)
<form action="{{ route('contratos.enviar_mk',$id) }}" method="post" class="delete_form" style="margin:0;display: inline-block;" id="enviar-mk-{{$id}}">
    @csrf
</form>
@endif

<a href="{{ route('contratos.show',$id )}}"  class="btn btn-outline-info btn-icons" title="Ver"><i class="far fa-eye"></i></a>
@if($state == 'enabled' && $plan_id)
    <a href="{{ route('contratos.grafica',$id )}}" class="btn btn-outline-dark btn-icons" title="Gráfica de Conexión"><i class="fas fa-chart-area"></i></a>
    <a href="{{ route('contratos.grafica_consumo',$id )}}" class="btn btn-outline-info btn-icons" title="Gráfica de Consumo"><i class="fas fa-chart-line"></i></a>
    <a href="{{ route('contratos.conexion',$id )}}" class="btn btn-outline-success btn-icons" title="Ping de Conexión"><i class="fas fa-plug"></i></a>
    <a href="{{ route('contratos.log',$id )}}" class="btn btn-outline-info btn-icons" title="Log de Contrato"><i class="fas fa-clipboard-list"></i></a>
@endif
<a href="{{ route('contratos.edit',$id )}}" class="btn btn-outline-primary btn-icons" title="Editar"><i class="fas fa-edit"></i></a>
@if($latitude && $longitude)
    <a href='https://www.google.com/maps/search/{{$latitude}},{{$longitude}}?hl=es' class="btn btn-outline-success btn-icons" title="Ver Ubicación Google Maps" target="_blank"><i class="fas fa-map-marked-alt"></i></a>
@endif
@if($plan_id || 1 == 1)
<button @if($state == 'enabled') class="btn btn-outline-danger btn-icons" title="Deshabilitar" @else class="btn btn-outline-success btn-icons" title="Habilitar" @endif type="submit" onclick="confirmar('cambiar-state{{$id}}', '¿Estas seguro que deseas cambiar el estatus del contrato?', '');"><i class="fas fa-file-signature"></i></button>
@endif
<button class="btn btn-outline-danger btn-icons" type="submit" title="Eliminar" onclick="confirmar('eliminar-contrato{{$id}}', '¿Está seguro que desea eliminar el contrato?', 'Se borrara de forma permanente');"><i class="fas fa-times"></i></button>
@if($firma_isp)
    <a href="{{ route('asignaciones.imprimir', $c_id )}}"  class="btn btn-outline-danger btn-icons" title="Imprimir Contrato Digital" target="_blank"><i class="fas fa-print"></i></i></a>
@endif
@if($mk == 0)
<button class="btn btn-outline-warning btn-icons" title="Enviar a MK" type="submit" onclick="confirmar('enviar-mk-{{$id}}', '¿Está seguro que desea registrar este contrato en la mikrotik?', '');"><i class="fas fa-server"></i></button>
@endif

<a href="{{route('factura.create.cliente', $c_id)}}" class="btn btn-outline-warning btn-icons" title="Crear una factura" target="_blank"><i class="fas fa-file-invoice-dollar"></i></a>

<a href="{{ route('contrato.iniciar', $id) }}" class="btn btn-outline-warning btn-icons" title="Iniciar contrato" onclick="return confirm('¿Estás seguro de iniciar el contrato?');"><i class="fas fa-clipboard-list"></i></a>

@if($olt_sn_mac != null)
<a href="#" class="btn {{$state_olt_catv == true ? 'btn-outline-success' : 'btn-outline-danger'}} btn-icons" title="{{$state_olt_catv == true ? 'Deshabilitar Catv?' : 'Habilitar Catv?'}}'"
onclick="confirmar('cambiar-statecatv{{$id}}', 
'¿Está seguro que desea cambiar el estado del catv a {{$state_olt_catv == true ? 'deshabilitado?' : 'habilitado?'}}', 
'Se actualizará su estado');"><i class="fas fa-tv"></i></a>
@endif