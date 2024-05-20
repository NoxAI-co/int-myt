@extends('layouts.app')
@section('content')
  <style>
    .readonly{ border: 0 !important;background-color: #f9f9f9 !important; }
    label, small { font-weight: 500; }
  </style>

    @if(Session::has('danger'))
    <div class="alert alert-danger" >
      {{Session::get('danger')}}
    </div>

    <script type="text/javascript">
      setTimeout(function(){
          $('.alert').hide();
          $('.active_table').attr('class', ' ');
      }, 5000);
    </script>
  @endif

  <form method="POST" action="{{ route('radicados.update', $radicado->id ) }}" style="padding: 2% 3%;    " role="form" class="forms-sample" novalidate id="form-banco" >
   {{ csrf_field() }}
      <input name="_method" type="hidden" value="PATCH">
      <div class="row">
    <div class="col-md-4 form-group">
      <label class="control-label">Nombre del Cliente</label>
      <input type="text" class="form-control readonly"  id="nombre" name="nombre"  required="" value="{{$radicado->nombre}}" maxlength="200" readonly="">
      <span class="help-block error">
        <strong>{{ $errors->first('nombre') }}</strong>
      </span>
    </div>

    <div class="col-md-4 form-group">
      <label class="control-label">Identificación</label>
      <input type="text" class="form-control readonly" id="ident" name="ident" readonly="" value="{{$radicado->identificacion}}" maxlength="20">
      <span class="help-block error">
        <strong>{{ $errors->first('identificacion') }}</strong>
      </span>
    </div>

    <div class="col-md-4 form-group">
      <label class="control-label">N° Contrato</label>
      <input type="text" class="form-control readonly"  id="contrato" name="contrato"  value="{{$radicado->contrato}}" maxlength="200" readonly="">
      <span class="help-block error">
        <strong>{{ $errors->first('contrato') }}</strong>
      </span>
    </div>

    <div class="col-md-4 form-group">
      <label class="control-label">N° Telefónico <span class="text-danger">*</span></label>
      <input type="text" class="form-control"  id="telefono" name="telefono"  required="" value="{{$radicado->telefono}}" maxlength="20">
      <span class="help-block error">
        <strong>{{ $errors->first('telefono') }}</strong>
      </span>
    </div>

    <div class="col-md-4 form-group">
      <label class="control-label">Correo Electrónico</label>
      <input type="email" class="form-control"  id="correo" name="correo"  value="{{$radicado->correo}}" maxlength="200">
      <span class="help-block error">
        <strong>{{ $errors->first('correo') }}</strong>
      </span>
    </div>

    <div class="col-md-12 form-group">
      <label class="control-label">Dirección <span class="text-danger">*</span></label>
      <input type="text" class="form-control"  id="direccion" name="direccion"  value="{{$radicado->direccion}}" maxlength="200" required="">
      <span class="help-block error">
        <strong>{{ $errors->first('direccion') }}</strong>
      </span>
    </div>

    <div class="col-md-3 form-group">
      <label class="control-label">Fecha <span class="text-danger">*</span></label>
      <input type="text" class="form-control datepicker"  id="fecha" name="fecha" required="" value="{{ date('d-m-Y', strtotime($radicado->fecha))}}" required="">
      <span class="help-block error">
        <strong>{{ $errors->first('fecha') }}</strong>
      </span>
    </div>

    <div class="col-md-3 form-group">
      <label class="control-label">Tipo de Servicio <span class="text-danger">*</span></label>
      <select class="form-control selectpicker" name="servicio" id="servicio" required="" title="Seleccione" required="">
        @foreach($servicios as $servicio)
          <option {{ $radicado->servicio==$servicio->id?'selected':''}} value="{{$servicio->id}}">{{$servicio->nombre}}</option>
        @endforeach
      </select>
      <span class="help-block error">
        <strong>{{ $errors->first('servicio') }}</strong>
      </span>
    </div>

    <div class="col-md-3 form-group">
      <label class="control-label">¿Escalar Caso? <span class="text-danger">*</span></label>
      <select class="form-control selectpicker" name="estatus" id="estatus" required="" title="Seleccione" onchange="searchDV(this.value)" required="">
          <option {{ $radicado->tecnico == NULL ? 'selected':'' }} value="0" selected>No</option>
          <option {{ $radicado->tecnico != NULL ? 'selected':'' }} value="2">Si</option>
      </select>
    </div>

    <div class="col-md-3 form-group" id="div_tecnico" style="display:{{ $radicado->tecnico != NULL ? 'block':'none' }};">
      <label class="control-label">Técnico Asociado <span class="text-danger">*</span></label>
      <select class="form-control selectpicker" name="tecnico" id="tecnico" required="" title="Seleccione">
        @foreach($tecnicos as $tecnico)
          <option {{ $radicado->tecnico == $tecnico->id?'selected':''}} value="{{$tecnico->id}}">{{$tecnico->nombres}}</option>
        @endforeach
      </select>
    </div>

    <div class="col-md-12 form-group">
      <label class="control-label">Observaciones <span class="text-danger">*</span></label>
      <textarea  class="form-control form-control-sm min_max_100" id="desconocido" required="" name="desconocido" value="{{ $radicado->desconocido }}">{{ $radicado->desconocido }}</textarea>
      <span class="help-block error">
        <strong>{{ $errors->first('desconocido') }}</strong>
      </span>
    </div>
  </div>
  <small>Los campos marcados con <span class="text-danger">*</span> son obligatorios</small>
  <hr>
  <div class="row" >
    <div class="col-sm-12" style="text-align: right;  padding-top: 1%;">
      <a href="{{route('radicados.index')}}" class="btn btn-outline-secondary">Cancelar</a>
      <button type="submit" class="btn btn-success">Guardar</button>
    </div>
  </div>
</form>
@endsection

@section('scripts')
  <script>
    function searchDV(id){
      option = id;
      if (option == 2) {
        document.getElementById("div_tecnico").style.display = "block";
      }else{
        document.getElementById("div_tecnico").style.display = "none";
        $("#tecnico").val('').selectpicker('refresh');
      }
    }
  </script>
@endsection