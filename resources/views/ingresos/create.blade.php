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
      }, 50000);
    </script>
  @endif
  @if(Session::has('danger'))
    <div class="alert alert-danger" >
      {{Session::get('danger')}}
    </div>
  @endif
  <div id="preloader" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(255,255,255,0.8); z-index:9999; text-align:center;">
    <div style="position:absolute; top:50%; left:50%; transform:translate(-50%, -50%); font-size:18px;">
        <span class="spinner-border text-primary" role="status" aria-hidden="true"></span><br>
        Enviando datos, por favor espera...
    </div>
  </div>

	<form method="POST" action="{{ route('ingresos.store') }}" style="padding: 2% 3%;" role="form" class="forms-sample" id="form-ingreso" enctype="multipart/form-data">
    @if($factura)
    <input type="hidden" id="factura" value="{{$factura}}">
    @endif
    {{-- Cliente que se escoge dinamicamente --}}
    <input type="hidden" id="clienteseleccionado" value={{ $cliente }}>
    <input type="hidden" id="saldofavorcliente" value={{ $saldo_favor }}>
    <h5>INFORMACIÓN GENERAL DEL INGRESO </h5>
  		{{ csrf_field() }}
  		<div class="row" style=" text-align: right; margin-top: 5%">
  			<div class="col-md-5">
	  			<div class="form-group row">
	  				<label class="col-sm-4 col-form-label">Cliente </label>
		  			<div class="col-sm-8">
		  				<select class="form-control selectpicker" name="cliente" id="cliente" title="Seleccione" data-live-search="true" data-size="5" onchange="factura_pendiente(); saldoContacto(this.value)">
		  				@foreach($clientes as $clien)
		              		<option {{old('cliente')==$clien->id?'selected':''}} {{$cliente==$clien->id?'selected':''}} {{$pers==$clien->id?'selected':''}} value="{{$clien->id}}">{{$clien->nombre}} {{$clien->apellido1}} {{$clien->apellido2}} - {{$clien->nit}}</option>
		  				@endforeach
            	</select>
		  			</div>

					<span class="help-block error">
			        	<strong>{{ $errors->first('cliente') }}</strong>
			    </span>
	  		</div>

        <div class="form-group row occultrd">
          <label class="col-sm-4 col-form-label">Cuenta <span class="text-danger">*</span><a><i data-tippy-content="Crea tus cuentas haciendo <a href='#'>clíck aquí</a>" class="icono far fa-question-circle"></i></a></label>
          <div class="col-sm-8">
            <select class="form-control selectpicker" name="cuenta" id="cuenta" title="Seleccione" data-live-search="true" data-size="5" required="">
              @php $tipos_cuentas=\App\Banco::tipos();@endphp
              @foreach($tipos_cuentas as $tipo_cuenta)
                <optgroup label="{{$tipo_cuenta['nombre']}}">

                  @foreach($bancos as $cuenta)
                    @if($cuenta->tipo_cta==$tipo_cuenta['nro'])
                      <option value="{{$cuenta->id}}" {{$banco==$cuenta->nro?'selected':''}} {{$bank==$cuenta->nro?'selected':''}} selected>{{$cuenta->nombre}}</option>
                    @endif
                  @endforeach
                </optgroup>
              @endforeach
            </select>
            <span class="help-block error">
                  <strong>{{ $errors->first('cuenta') }}</strong>
            </span>
          </div>


      </div>
      <div class="form-group row occultrd">
          <label class="col-sm-4 col-form-label">Método de pago </label>
          <div class="col-sm-8">
            <select class="form-control selectpicker" name="metodo_pago" id="metodo_pago" title="Seleccione" data-live-search="true" data-size="5">
              @foreach($metodos_pago as $metodo)
                    <option value="{{$metodo->id}}" @if(Auth::user()->id == 21) {{$metodo->id==9?'selected':''}}  @else {{$metodo->id==1?'selected':''}} @endif>{{$metodo->metodo}}</option>
                @endforeach
            </select>
          </div>

        <span class="help-block error">
              <strong>{{ $errors->first('metodo_pago') }}</strong>
        </span>
      </div>
      <div class="form-group row">
        <label class="col-sm-4 col-form-label">Realizar un</label>
        <div class="col-sm-8">
          <select class="form-control selectpicker" name="realizar" id="realizar" title="Seleccione" data-live-search="true" data-size="5" onchange="showAnti()">
              <option value="1" selected>Pago a Factura o Categoría</option>
              <option value="2" >Anticipo</option>
          </select>
        </div>

      <span class="help-block error">
            <strong>{{ $errors->first('realizar') }}</strong>
      </span>
    </div>

    <div class="cls-realizar-inv">
        <div class="form-group row">
          <label class="col-sm-4 col-form-label">Forma de pago</label>
          <div class="col-sm-8">
            <select class="form-control selectpicker" name="forma_pago" id="forma_pago" title="Seleccione" data-live-search="true" data-size="5" onchange="showAnti()">
              @foreach($formas as $f)
              <option value="{{$f->id}}">{{$f->codigo}} - {{$f->nombre}}</option>
              @endforeach
            </select>
          </div>

        <span class="help-block error">
              <strong>{{ $errors->first('realizar') }}</strong>
        </span>
      </div>
    </div>

    <div class="form-group row cls-realizar d-none" >
       <div class="form-group row ">
      <label class="col-sm-4 col-form-label">Donde ingresa el dinero <span class="text-danger">*</span></label>
      <div class="col-sm-8">
        <select class="form-control selectpicker" name="puc" id="puc" title="Seleccione" data-live-search="true" data-size="5" required>
          @foreach($categorias as $categoria)
            <option value="{{$categoria->id}}" >{{$categoria->nombre}} - {{$categoria->codigo}}</option>
          @endforeach
        </select>
      </div>

    <span class="help-block error">
          <strong>{{ $errors->first('puc') }}</strong>
    </span>
       </div>
  </div>
    <div class="form-group row cls-realizar d-none" >
      <div class="form-group row ">
    <label class="col-sm-4 col-form-label">Cuenta del anticipo <span class="text-danger">*</span></label>
    <div class="col-sm-8">
      <select class="form-control selectpicker" name="anticipo" id="anticipo" title="Seleccione" data-live-search="true" data-size="5" required>
        @foreach($anticipos as $anticipo)
          <option value="{{$anticipo->id}}" >{{$anticipo->nombre}} - {{$anticipo->codigo}}</option>
        @endforeach
      </select>
    </div>

  <span class="help-block error">
        <strong>{{ $errors->first('anticipo') }}</strong>
  </span>
      </div>
  </div>
    <div class="cls-realizar d-none" >
      <div class="form-group row ">
        <label class="col-sm-4 col-form-label">Valor Recibido <span class="text-danger">*</span></label>
        <div class="col-sm-8">
          <input type="number" class="form-control" name="valor_recibido" id="valor_recibido" required>
        </div>

      <span class="help-block error">
            <strong>{{ $errors->first('valor_recibido') }}</strong>
      </span>
      </div>
    </div>
      <div class="form-group row">
        <label class="col-sm-4 col-form-label">Fecha</label>
        <div class="col-sm-8">
          <input type="text" class="form-control datepicker"  id="fecha" value="{{date('d-m-Y')}}" name="fecha" disabled=""  >
        </div>
      </div>

    <div class="form-group row d-none" id="divusarsaldo">
        <label class="col-sm-4 col-form-label">¿utilizar saldo a favor del ciente? <a><i
                        data-tippy-content="Si está opcion te aparece es por que el cliente escogido tiene un saldo a favor y puedes pagar las facturas con ese saldo."
                        class="icono far fa-question-circle"></i></a></label>
        <div class="col-sm-8">
            <div class="form-group row">
                <div class="col-sm-4">
                    <div class="form-radio">
                        <label class="form-check-label">
                            <input type="radio" class="form-check-input" name="uso_saldo" id="publico1"
                                    value="1" onchange="hidedivtwo('occultrd');"> Si
                            <i class="input-helper"></i><i class="input-helper"></i></label>
                    </div>
                </div>

                <div class="col-sm-4">
                    <div class="form-radio">
                        <label class="form-check-label">
                            <input type="radio" class="form-check-input" name="uso_saldo" id="publico"
                                    value="0" onchange="showdivtwo('occultrd');" checked=""> No
                            <i class="input-helper"></i><i class="input-helper"></i></label>
                    </div>
                </div>
            </div>
        </div>
    </div>

        <style type="text/css">
          .form-radio label input + .input-helper:before{
            border:1px solid #000;
          }
        </style>


<input type="hidden" >
<div class="col-md-12 d-none" style="background: #80808061;border: 1px solid #80808061;" id="saldo123">
    <div class="row">
      <div class="col-md-4 text-right" style="    padding: 4%; font-weight: bold; color:#808080 ">Saldo Favor</div>
        <input class="col-md-8 text-left text-danger" style="padding: 4%; font-weight: bold" name="total_saldo" id="total_saldo" type="text" value="0" disabled>
    </div>
  </div>


		</div>
		<div class="col-md-5 offset-md-2">
		    <div class="form-group row">
                      <label class="col-sm-4 col-form-label">Nro</label>
                      <div class="col-sm-8">
                          <input type="text" class="form-control" value="{{$numero}}" readonly disabled>
                      </div>
                  </div>
    			<div class="form-group row">
          <label class="col-sm-4 col-form-label">Observaciones</label>
          <div class="col-sm-8">
            <textarea  class="form-control min_max_100" name="observaciones"></textarea>
          </div>
  			</div>

        <div class="form-group row">
          <label class="col-sm-4 col-form-label">Notas del recibo <small>Visibles al imprimir</small></label>
          <div class="col-sm-8">
            <textarea  class="form-control min_max_100" name="notas"></textarea>
          </div>
        </div>
        <small>Los campos marcados con <span class="text-danger">*</span> son obligatorios</small>
  		</div>
  		</div>

    <h5>TIPO DE TRANSACCIÓN</h5>
    <div class="row cls-realizar-inv" style=" margin-top: 5%; text-align: center;">
      <div class="col-md-12">
        <h6>¿Asociar este ingreso a una factura de venta existente? <a><i data-tippy-content="<font color='#d08f50'>Si</font> para cancelar o abonar facturas <br><font color='#d08f50'>No</font> para registrar otros ingresos" class="icono far fa-question-circle"></i></a></h6>
        <p>Recuerda que puedes registrar un ingreso sin necesidad de que esté asociado a una factura de venta</p>
        <div class="row">
          <div class="col-sm-1 offset-sm-5">
          <div class="form-radio">
            <label class="form-check-label">
            <input type="radio" class="form-check-input" name="tipo" id="tipo1" value="1" onchange="showdiv('si'); hidediv('no'); factura_pendiente();" {{$factura?'checked':'' }}> Si
            <i class="input-helper"></i></label>
          </div>
        </div>
        <div class="col-sm-1">
          <div class="form-radio">
            <label class="form-check-label">
            <input type="radio" class="form-check-input" name="tipo" id="tipo" value="2" onchange="showdiv('no');  hidediv('si');"> No
            <i class="input-helper"></i></label>
          </div>
        </div>
        </div>
      </div>
    </div>
  		<div class="row cls-realizar-inv">
        <div class="col-md-12 fact-table" id="si" style="display: none;">
          <h5>FACTURAS DE VENTA PENDIENTES</h5>
          <div id="factura_pendiente"></div>
        </div>
  			<div class="col-md-12 fact-table" id="no" style="display: none;">
          <h5>¿A QUÉ CATEGORÍA(S) PERTENECE ESTE INGRESO?</h5>
          <div id="div-categoria">
            <table class="table table-striped table-sm" id="table-form" width="100%">
            	<thead class="thead-dark">
            		<tr>
            			<th width="28%">Categoria</th>
                  <th width="8%">Valor</th>
            			<th width="12%">Impuesto</th>
                  <th width="7%">Cantidad</th>
            			<th width="13%">Observaciones</th>
            			<th width="10%">Total</th>
                  <th width="2%"></th>
            		</tr>
            	</thead>
            	<tbody>
            		<tr id="1">
                  <td  class="no-padding">
                      <div class="resp-item">
                    <select class="form-control form-control-sm selectpicker no-padding"  title="Seleccione" data-live-search="true" data-size="5" name="categoria[]" id="categoria1" required="" onchange="enabled(1);" >
              				@foreach($categorias as $categoria)
                              <option value="{{$categoria->id}}">{{$categoria->nombre}}</option>


                      @endforeach
              			</select>
              			</div>
					  			</td>
            			<td class="monetario">
            			    <div class="resp-precio">
  				          <input type="number" class="form-control form-control-sm" id="precio_categoria1" name="precio_categoria[]" placeholder="Precio" onchange="total_linea(1)" maxlength="24" min="0" required="" disabled="">
	  			        </div>
	  			        </td>
            			<td>
                    <select class="form-control form-control-sm selectpicker" name="impuesto_categoria[]" id="impuesto_categoria1" title="Impuesto" onchange="total_categorias();" required="" disabled="">
                        @foreach($impuestos as $impuesto)
                          <option value="{{$impuesto->id}}" porc="{{$impuesto->porcentaje}}">{{$impuesto->nombre}} - {{$impuesto->porcentaje}}%</option>
                        @endforeach
                    </select>
	  			        </td>
                  <td width="5%">
                    <input type="number" class="form-control form-control-sm" id="cant_categoria1" name="cant_categoria[]" placeholder="Cantidad" onchange="total_linea(1);" min="1" required="" disabled="">
                  </td>
            			<td  style="padding-top: 1% !important;">
            			<div class="resp-observaciones">
						        <textarea  class="form-control form-control-sm" id="descripcion_categoria1" name="descripcion_categoria[]" placeholder="Observaciones" disabled=""></textarea>
            			</div>
            			</td>
            			<td>
            			    <div class="resp-total">
	  				        <input type="text" class="form-control form-control-sm text-right" id="total_categoria1" value="0.00" disabled="">
        	  			    </div>
        	  			</td>
	  			      <td><button type="button" class="btn btn-outline-secondary btn-icons" onclick="Eliminar(1);">X</button></td>
          		</tr>
            </tbody>
          </table>
          <button class="btn btn-outline-primary" onclick="CrearFilaCategorias();" type="button" style="margin-top: 5%; margin-bottom: 1%;">Agregar línea</button>


          {{-- FORMAS DE PAGO Y RETENCIONES PARA CUANDO ENTRA DINERO (RECIBO DE CAJA) POR UNA CATEGORIA --}}
          <div class="row">
            <div class="col-md-5 no-padding">
              <h5>¿ TE APLICARON ALGUNA RETENCIÓN ?</h5>
              <table class="table table-striped table-sm" id="table-retencion">
                <thead class="thead-dark">
                  <th width="60%">Tipo de Retención</th>
                  <th width="34%">Valor</th>
                  <th width="5%"></th>
                </thead>
                <tbody>
                </tbody>
              </table>
              <button class="btn btn-outline-primary" onclick="CrearFilaRetencion();" type="button" style="margin-top: 2%;">Agregar Retención</button>
            </div>
            <div class="col-md-7">
              <h5>FORMAS DE PAGO <a><i data-tippy-content="Elige a que cuenta ira enlazado el movimiento contable" class="icono far fa-question-circle"></i></a></h5>
                  <table class="table table-striped table-sm" id="table-formaspago-cat">
                    <thead class="thead-dark">
                      <th width="50%">Cuenta</th>
                      <th width="25%">Cruce</th>
                      <th width="20%" class="no-padding">Valor</th>
                      <th width="5%"></th>
                    </thead>
                    <tbody>
                    </tbody>
                  </table>
                  <div class="row">
                    <div class="col-md-6">
                      <button class="btn btn-outline-primary" onclick="CrearFilaFormaPago(true);" type="button" style="margin-top: 2%;">Agregar forma de pago</button><a><i data-tippy-content="Agrega nuevas formas de pago haciendo <a href='#'>clíck aquí</a>" class="icono far fa-question-circle"></i></a>
                    </div>
                    <div class="col-md-6 d-flex justify-content-between pt-3">
                      <h5>Total:</h5>
                      <span>$</span><span id="anticipototal">0</span>
                    </div>
                    <div class="col-md-12">
                      <span class="text-danger" style="font-size:12px"><strong>El total de las formas de pago debe coincidir con el total neto</strong></span>
                    </div>
                  </div>
              </div>
          </div>


      <div class="row" style="margin-top: 5%;">
        <div class="col-md-4 offset-md-8">
          <table style="text-align: right;  width: 100%;" id="totales">
            <tr>
              <td width="40%">Subtotal</td>
              <input type="hidden" id="subtotal_categoria_js" value="0">
              <input type="hidden" id="impuestos_categoria_js" value="0">
              <td>{{Auth::user()->empresa()->moneda}} <span id="subtotal_categoria">0</span></td>
            </tr>
          </table>
          <table style="text-align: right; width: 100%;" id="totalesreten">
            <tbody></tbody>
          </table>
          <hr>
          <table style="text-align: right; font-size: 24px !important; width: 100%;">
            <tr>
              <td width="40%">TOTAL</td>
              <td>{{Auth::user()->empresa()->moneda}} <span id="total_categoria">0</span></td>
            </tr>
          </table>
        </div>
        </div>
      </div>
  		</div>
</div>

  		<hr>

      <div class="row">
        <div class="col-md-6">
          {{-- <div class="form-group cls-anticipo d-none">
                <div class="form-group">
              <label class="col-form-label">Donde ingresa el dinero <span class="text-danger">*</span></label>


                <select class="form-control selectpicker" name="puc_banco" id="puc_banco" title="Seleccione" data-live-search="true" data-size="5" required>
                  @foreach($formas as $f)
                    <option value="{{$f->id}}" >{{$f->nombre}} - {{$f->codigo}}</option>
                  @endforeach
                </select>

            <span class="help-block error">
                  <strong>{{ $errors->first('puc_banco') }}</strong>
            </span>
                </div>
          </div>  --}}
        </div>
        <div class="col-md-6">
          <div class="form-group cls-anticipo d-none">
              <div class="form-group">

                <label class="col-form-label">Cuenta del anticipo <span class="text-danger">*</span></label>
                <select class="form-control selectpicker" name="anticipo_factura" id="anticipo_factura" title="Seleccione" data-live-search="true" data-size="5" required>
                  @foreach($anticipos as $anticipo)
                    <option value="{{$anticipo->id}}" >{{$anticipo->nombre}} - {{$anticipo->codigo}}</option>
                  @endforeach
                </select>

                <span class="help-block error">
                      <strong>{{ $errors->first('anticipo_factura') }}</strong>
                </span>
              </div>
          </div>
        </div>
      </div>

      <div class="row form-inline">
        <div class="col-md-4 form-inline">
          <div class="form-group inline-block">
            <label class="mr-2 form-label">Nro Comprobante de Pago</label>
            <input type="text" class="form-control form-control-sm"  id="comprobante_pago" name="comprobante_pago" value="{{old('comprobante_pago')}}" style="width: 40%;">
          </div>
        </div>
        <div class="col-md-4 form-inline">
          <div class="form-group inline-block">
            <label class="mr-2 form-label">Soporte de Pago</label>
            <input type="file" class="form-control form-control-sm"  id="adjunto_pago" name="adjunto_pago" value="{{old('adjunto_pago')}}" accept=".jpg, .jpeg, .png, .pdf" style="width: 60%;">
          </div>
        </div>
        <div class="col-md-2">
          <div class="form-check form-check-flat">
            <label class="form-check-label">
              <input type="checkbox" class="form-check-input" name="tirilla" value="1">Imprimir Tirilla
              <i class="input-helper"></i>
            </label>
          </div>
        </div>
       
        @if(Auth::user()->empresa()->isWhatsapp() == 1)
        <div class="col-md-3">
          <div class="form-check form-check-flat">
            <label class="form-check-label">
              <input type="checkbox" class="form-check-input" name="tirilla_wpp" value="1">Enviar tirilla por whatsapp
              <i class="input-helper"></i>
            </label>
          </div>
        </div>
        @endif
      </div>
      <hr>


      <input type="hidden" @if($contrato) value="{{$contrato->opciones_dian}}" @else value="0" @endif id="input-ingresos-electronica">
      @if($contrato)
        @if($contrato->cliente()->boton_emision == 1)
      <div class="row form-inline fact-table" id="form-ingresos-electronica">
          <div class="col-md-3">
              <div class="form-radio">
                  <label class="form-check-label" style="font-size: 13px;">
                      <input type="radio" class="form-check-input" name="tipo_electronica" value="3">Crear próxima factura
                      <i class="input-helper"></i>
                  </label>
              </div>
          </div>
          <div class="col-md-3">
              <div class="form-radio">
                  <label class="form-check-label" style="font-size: 13px;">
                      <input type="radio" class="form-check-input" name="tipo_electronica" value="4">Prorratear
                      <i class="input-helper"></i>
                  </label>
              </div>
          </div>
        <div class="col-md-3">
          <div class="form-radio">
            <label class="form-check-label" style="font-size: 13px;">
              <input type="radio" class="form-check-input" name="tipo_electronica" value="1">Convertir a electrónica
              <i class="input-helper"></i>
            </label>
          </div>
        </div>
        <div class="col-md-3">
          <div class="form-radio">
            <label class="form-check-label" style="font-size: 13px;">
              <input type="radio" class="form-check-input" name="tipo_electronica" value="2">Convertir a electrónica y emitir
              <i class="input-helper"></i>
            </label>
          </div>
        </div>
      <div class="col-md-6">
          <div class="alert alert-warning" role="alert">
              Se tendrán en cuenta todas las facturas que les asocie un pago.
              <a><i data-tippy-content="Las facturas que ya sean electrónicas, podrá hacer uso de la funcion 'convertir a electrónica y emitir'.
          Si por lo contrario selecciona la opcion 'convertir a electrónica' no tendrá ningun efecto sobre las que ya son electrónicas." class="icono far fa-question-circle"></i></a>
          </div>
      </div>
      </div>
        @endif
      @endif

  		<div class="row mt-2">
        <div class="col-sm-12" style="text-align: right;  padding-top: 1%;">
          <a href="{{route('ingresos.index')}}" class="btn btn-outline-secondary">Cancelar</a>
          <button type="button" id="submitcheck" class="btn btn-success" id="button-guardar">Pagar</button>
        </div>
  		</div>
  	</form>
  	<input type="hidden" id="url" value="{{url('/')}}">
    <input type="hidden" id="impuestos" value="{{json_encode($impuestos)}}">
    <input type="hidden" id="retenciones" value="{{json_encode($retenciones)}}">
    <input type="hidden" id="simbolo" value="{{Auth::user()->empresa()->moneda}}">
    <input type="hidden" id="formaspago" value="{{json_encode($relaciones)}}">

    {{-- VARIABLE DE SALDO A FAVOR DEL CLIENTE --}}
    <input type="hidden" id="saldofavorcliente" name="saldofavorcliente">

    <input type="hidden" id="allcategorias" value='@foreach($categorias as $categoria)
  <optgroup label="{{$categoria->nombre}}">
      @foreach($categoria->hijos(true) as $categoria1)
        <option {{old('categoria')==$categoria1->id?'selected':''}} value="{{$categoria1->id}}" {{$categoria1->estatus==0?'disabled':''}}>{{$categoria1->nombre}}</option>
        @foreach($categoria1->hijos(true) as $categoria2)
            <option class="hijo" {{old('categoria')==$categoria2->id?'selected':''}} value="{{$categoria2->id}}" {{$categoria2->estatus==0?'disabled':''}}>{{$categoria2->nombre}}</option>
          @foreach($categoria2->hijos(true) as $categoria3)
            <option class="nieto" {{old('categoria')==$categoria3->id?'selected':''}} value="{{$categoria3->id}}" {{$categoria3->estatus==0?'disabled':''}}>{{$categoria3->nombre}}</option>
            @foreach($categoria3->hijos(true) as $categoria4)
              <option class="bisnieto" {{old('categoria')==$categoria4->id?'selected':''}} value="{{$categoria4->id}}" {{$categoria3->estatus==0?'disabled':''}}>{{$categoria4->nombre}}</option>

            @endforeach

          @endforeach

        @endforeach
      @endforeach
  </optgroup>
@endforeach'>
@endsection

@section('scripts')
<script src="{{asset('lowerScripts/ingreso/ingreso.js')}}"></script>
<script>
  $(document).ready(function(){
    //validacion
    let cliente = $("#clienteseleccionado").val();
    let saldoFavor = $("#saldofavorcliente").val();
  $('.fecha').datepicker({
      locale: 'es-es',
      uiLibrary: 'bootstrap4',
      format: 'yyyy-mm-dd' ,
  });
    if(cliente && saldoFavor > 0){
        $("#divusarsaldo").removeClass('d-none');
        $("#saldo123").removeClass('d-none');
        $("#total_saldo").val(saldoFavor);
    }

      let opcion = $("#input-ingresos-electronica").val();

      if(opcion == 0){
        $("#form-ingresos-electronica").addClass('d-none');
      }

      $("#submitcheck").click(function (e) {
    e.preventDefault();

    var form = $("#submitcheck").closest('form');

    // QUITAR 'required' de los campos que estén ocultos
    form.find(':input').each(function() {
        var input = $(this);
        if (!input.is(':visible')) {
            input.prop('required', false);
        }
    });

    // Validar si el formulario es válido
    if (!form[0].checkValidity()) {
        form[0].reportValidity();
        return;
    }

    var btn = document.getElementById('button-guardar');
    var preloader = $("#preloader");

    // Mostrar el preloader
    preloader.show();

    // Deshabilitar el botón para evitar más clics
    setTimeout(function () {
        btn.setAttribute('disabled', 'disabled');
    }, 1);

    // Rehabilitar el botón y ocultar el preloader después de 45 segundos
    setTimeout(function () {
        btn.removeAttribute('disabled');
        preloader.hide();
    }, 45000);

    // Enviar el formulario
    form.submit();
});


  })
</script>
@endsection
