@extends('layouts.app')

@section('boton')
<a href="javascript:abrirFiltrador()" class="btn btn-info btn-sm my-1" id="boton-filtrar"><i class="fas fa-search"></i>Filtrar</a>
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

    <form id="form-dinamic-action" method="GET">
        <div class="container-fluid mb-3" id="form-filter">
            <fieldset>
                <legend>Filtro de Búsqueda</legend>
                <div class="card shadow-sm border-0">
                    <div class="card-body py-3" style="background: #f9f9f9;">
                        <div class="row">
                            <div class="col-md-4 pl-1 pt-1">
                                <select title="Olt a buscar" class="form-control selectpicker" id="olt_id" name="olt_id" data-size="5" data-live-search="true" onchange="oltChange(this.value)">
                                    @foreach($olts as $olt)
                                        <option value="{{ $olt['id'] }}" {{ $olt['id'] == $olt_default ? 'selected' : '' }}>{{ $olt['name'] }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                    </div>
                </div>
            </fieldset>
        </div>
    </form>

    <div class="row card-description">
        <div class="col-md-12">
            <table class="table table-striped table-hover" id="table-general">
                <thead class="thead-dark">
                <tr>
                    <th width='3%'>PON Type</th>
                    <th width='3%'>Board</th>
                    <th width='3%'>Port</th>
                    <th width='1%'>Pon Description</th>
                    <th width='3%'>SN</th>
                    <th width='3%'>Type</th>
                    <th width='3%'>Estatus</th>
                    <th width='3%'>Action</th>
                </tr>
                </thead>
                <tbody>
                @for ($i=0; $i < count($onus); $i++)
                    @if($onus[$i]['olt_id'] == $olt_default)
                    <tr id="olt_{{$i}}">
                        <td>{{ $onus[$i]['pon_type'] }}</td>
                        <td>{{ $onus[$i]['board'] }}</td>
                        <td>{{ $onus[$i]['port'] }}</td>
                        <td>{{ $onus[$i]['pon_description'] }}</td>
                        <td>{{ isset($onus[$i]['sn']) ? $onus[$i]['sn'] : '' }}</td>
                        <td>{{ $onus[$i]['onu_type_name'] }}</td>
                        <td>{{ $onus[$i]['is_disabled'] == 1 ? 'Innactivo' : 'Activo' }}</td>
                        <td>
                            @if($onus[$i]['is_disabled'] == 0)
                            {{-- <a href="#" onclick="authorizeOnu({{$i}})">Authorize</a> --}}
                            <a href="#" onclick="formAuthorizeOnu({{$i}})">Authorize</a>
                            @endif
                        </td>
                    </tr>
                    @endif
                @endfor
                </tbody>
            </table>
        </div>
    </div>

@endsection
@section('scripts')
    <script>

        function formAuthorizeOnu(index){

            let row = document.getElementById('olt_' + index);

            let ponType = row.cells[0].innerText;
            let board = row.cells[1].innerText;
            let port = row.cells[2].innerText;
            let ponDescription = row.cells[3].innerText;
            let sn = row.cells[4].innerText;
            let onuTypeName = row.cells[5].innerText;
            let status = row.cells[6].innerText;

            // Construir la URL con los parámetros GET
            let url = `{{ route('olt.form-authorized-onus') }}?ponType=${encodeURIComponent(ponType)}&board=${encodeURIComponent(board)}&port=${encodeURIComponent(port)}&ponDescription=${encodeURIComponent(ponDescription)}&sn=${encodeURIComponent(sn)}&onuTypeName=${encodeURIComponent(onuTypeName)}&status=${encodeURIComponent(status)}`;

            // Redirigir a la URL
            window.location.href = url;

        }

        function authorizeOnu(index){

            if (window.location.pathname.split("/")[1] === "software") {
				var url='/software/Olt/authorized-onus';
			}else{
				var url = '/Olt/authorized-onus';
			}

            let row = document.getElementById('olt_' + index);

            let ponType = row.cells[0].innerText;
            let board = row.cells[1].innerText;
            let port = row.cells[2].innerText;
            let ponDescription = row.cells[3].innerText;
            let sn = row.cells[4].innerText;
            let onuTypeName = row.cells[5].innerText;
            let status = row.cells[6].innerText;

            $.ajax({
                url: url,
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                method: 'get',
                data: {
                    ponType,
                    board,
                    port,
                    ponDescription,
                    sn,
                    onuTypeName,
                    status
                },
                success: function (data) {			
                }
            });
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
            $('#nro').val('');
            $('#client_id').val('').selectpicker('refresh');
            $('#plan').val('').selectpicker('refresh');

            $('#form-filter').addClass('d-none');
            $('#boton-filtrar').html('<i class="fas fa-search"></i> Filtrar');
            // getDataTable();
        }

        // $('#form-filter').on('keypress',function(e) {
        //         if(e.which == 13) {
        //             getDataTable();
        //             return false;
        //         }
        // });

        // function getDataTable() {
        //     $('#table-general').DataTable().ajax.reload();
	    // }

        function oltChange(id){

            let url = `{{ route('olt.unconfigured') }}?olt=${id}`;
            window.location.href = url;
        }
        
    </script>
@endsection
