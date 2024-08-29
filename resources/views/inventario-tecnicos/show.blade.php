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

    @if(Session::has('error'))
        <div class="alert alert-danger" >
            {{Session::get('error')}}
        </div>

        <script type="text/javascript">
            setTimeout(function(){
                $('.alert').hide();
                $('.active_table').attr('class', ' ');
            }, 5000);
        </script>
    @endif

    @if(Session::has('success-newcontact'))
        <div class="alert alert-success" style="text-align: center;">
            {{Session::get('success-newcontact')}}
        </div>

        <script type="text/javascript">
            setTimeout(function(){
                $('.alert').hide();
                $('.active_table').attr('class', ' ');
            }, 5000);
        </script>
    @endif

    <style>
        #titulo{
            display:none;
        }
    </style>

    <div class="paper">
        <!--Formulario Facturas-->
        <form method="POST" action="{{ route('asignacionmaterial.store') }}" style="padding: 2% 3%;    " role="form" class="forms-sample" novalidate id="form-factura" >
            {{ csrf_field() }}

            <div class="row text-right">
                <div class="col-md-5">
                    <div class="form-group row">
                        <label class="col-sm-4 col-form-label">Tecnico</label>
                        <div class="col-sm-8">
                            <div class="input-group">
                                <input type="text" class="form-control"  value="{{$tecnico->nombres}}"  disabled=""  >
                            </div>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-4 col-form-label">Email </label>
                        <div class="col-sm-8">
                            <input type="text" class="form-control"  value="{{$tecnico->email}}"  disabled=""  >
                        </div>
                    </div>
                </div>
                <div class="col-md-5">
                    <div class="form-group row">
                        <label class="col-sm-4 col-form-label">Tipo</label>
                        <div class="col-sm-8">
                            <div class="input-group">
                                <select class="form-control selectpicker"  id="type" required="">
                                    <option value="" disabled>Seleccionar</option>
                                    <option value="ingresos" {{ $type == "ingresos"?'selected':'' }}>Asignados al Técnico</option>
                                    <option value="salidas" {{ $type == "salidas"?'selected':'' }}>Asignados por el Técnico</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-4 col-form-label">Agrupar </label>
                        <div class="col-sm-8">
                            <select class="form-control selectpicker"  id="group" required="">
                                <option value="" disabled>Seleccionar</option>
                                <option value="agrupar" {{ $group == "agrupar"?'selected':'' }}>Agrupar por producto</option>
                                <option value="sinagrupar" {{ $group == "sinagrupar"?'selected':'' }}>Sin agrupar</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
            <div class="alert alert-warning nopadding onlymovil" style="text-align: center;">
                <button type="button" class="close" data-dismiss="alert">×</button>
                <strong><small><i class="fas fa-angle-double-left"></i> Deslice <i class="fas fa-angle-double-right"></i></small></strong>
            </div>
            <hr>
            <div class="fact-table">
                <div class="row">
                    <div class="col-md-12">
                        @if( $group =="agrupar")
                            <table class="table table-striped table-hover" id="table-general">
                                <thead class="thead-dark">
                                <tr>
                                    <th>Nombre de material</th>
                                    <th>Referencia</th>
                                    <th>Cantidad</th>
                                    <th>Acciones</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach ($materials as $material)
                                    <tr>
                                        <td>{{$material->material->producto}}</td>
                                        <td>{{$material->material->ref}}</td>
                                        <td>{{$material->total_cantidad}}</td>
                                        <td></td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        @else
                            <table class="table table-striped table-hover" id="table-general">
                                <thead class="thead-dark">
                                <tr>
                                    <th>Nombre de material</th>
                                    <th>Referencia</th>
                                    <th>Cantidad</th>
                                    <th>Fecha</th>
                                    <th>Acciones</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach ($materials as $material)
                                    <tr>
                                        <td>{{$material->material->producto}}</td>
                                        <td>{{$material->material->ref}}</td>
                                        <td>{{$material->cantidad}}</td>
                                        <td>{{ date_format($material->created_at, 'd-m-Y') }}</td>
                                        <td></td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        @endif
                    </div>
                </div>
                <hr>
                <div class="row ">
                    <div class="col-sm-12 text-right" style="padding-top: 1%;">
                        <a href="{{route('inventarioTecnicos.index')}}" class="btn btn-outline-secondary">Regresar</a>
                    </div>
                </div>
            </div>
        </form>
    </div>
@endsection

@section('scripts')
    <script>
        $(document).ready(function(){
            $('#type, #group').change(function(){
                console.log("cambió")
                var type = document.getElementById('type').value;
                var group = document.getElementById('group').value;
                var tecnicoId = '{{ $tecnico->id }}';

                if (type && group) {
                    var url = '{{ route("inventarioTecnicos.show", [":id", ":type",":group"]) }}';
                    url = url.replace(':id', tecnicoId);
                    url = url.replace(':type', type);
                    url = url.replace(':group', group);
                    window.location.href = url;
                }
            });
        });
    </script>
@endsection
