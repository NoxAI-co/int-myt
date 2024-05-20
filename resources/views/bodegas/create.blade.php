@extends('layouts.app')
@section('content')
	<form method="POST" action="{{ route('bodegas.store') }}" style="padding: 2% 3%;    " role="form" class="forms-sample" novalidate id="form-general" >
   {{ csrf_field() }}
  <div class="row">

    <div class="col-md-4 form-group">
      <label class="control-label">Nombre<span class="text-danger">*</span></label>
      <input type="text" class="form-control"  id="nombre" name="nombre"  required="" value="{{old('nombre')}}" maxlength="200">
      <span class="help-block error">
        <strong>{{ $errors->first('nombre') }}</strong>
      </span>
    </div>
    <div class="col-md-4 form-group">
      <label class="control-label">Dirección</label>
      <textarea  class="form-control form-control-sm" name="direccion">{{old('direccion')}}</textarea>
    </div>
    <div class="col-md-4 form-group">
      <label class="control-label">Observaciones</label>
      <textarea  class="form-control form-control-sm" name="descripcion">{{old('observaciones')}}</textarea>
    </div>
  </div>
  <small>Los campos marcados con <span class="text-danger">*</span> son obligatorios</small>
  <hr>
	<div class="row" >
    <div class="col-sm-12" style="text-align: right;  padding-top: 1%;">
      <a href="{{route('bodegas.index')}}" class="btn btn-outline-secondary">Cancelar</a>
      <button type="submit" id="submitcheck" onclick="submitLimit(this.id)" class="btn btn-success">Guardar</button>
    </div>
	</div>
</form>
@endsection