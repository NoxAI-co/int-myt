@extends('layouts.app')

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

    @if(Session::has('danger'))
        <div class="alert alert-danger">
            {{Session::get('danger')}}
        </div>
        <script type="text/javascript">
            setTimeout(function() {
                $('.alert').hide();
                $('.active_table').attr('class', ' ');
            }, 5000);
        </script>
    @endif

    @if(Session::has('message_denied'))
        <div class="alert alert-danger" role="alert">
            {{Session::get('message_denied')}}
            @if(Session::get('errorReason'))<br> <strong>Razon(es): <br></strong>
            @if(count(Session::get('errorReason')) > 1)
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

    <div class="row card-description">
        <div class="col-md-12">
            <div class="container-filtercolumn form-inline">

            </div>
        </div>
        <div class="col-md-12">
            <table class="table table-striped table-hover" id="table-general">
                <thead class="thead-dark">
                <tr>
                    <th></th>
                    <th>Nombre de Tecnico</th>
                    <th>Email de Tecnico</th>
                    <th>Fecha</th>
                    <th>Acciones</th>
                </tr>
                </thead>
                <tbody>
                @foreach ($tecnicos as $tecnico)
                    <tr>
                        <td></td>
                        <td>{{$tecnico->nombres}}</td>
                        <td>{{$tecnico->email}}</td>
                        <td>{{date_format($tecnico->created_at,'d-m-Y')}}</td>
                        <td>
                            <a href="{{ route('inventarioTecnicos.show', [$tecnico->id, "ingresos","sinagrupar"]) }}"  class="btn btn-outline-primary btn-icons" title="Ver"><i class="fas fa-eye"></i></a>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection
@section('scripts')
    <script>
    </script>
@endsection