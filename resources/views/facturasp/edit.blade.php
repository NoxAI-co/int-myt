@extends('layouts.app')
@section('content')
  <form method="POST" action="{{ route('facturasp.update', $factura->id) }}" style="padding: 2% 3%;" role="form" class="forms-sample" novalidate id="form-factura" >
      {{ csrf_field() }}
      <input name="_method" type="hidden" value="PATCH">
    <input type="hidden" value="1" name="cotizacion" id="cotizacion_si">
    <input type="hidden" value="1" name="orden_si" id="orden_si">
    <input type="hidden" value="1" name="fact_prov" id="fact_prov">
    <div class="row text-right">
      <div class="col-md-5">
        <div class="form-group row">
          <label class="col-sm-4 col-form-label">Proveedor <span class="text-danger">*</span></label>
          <div class="col-sm-8">
            <div class="input-group">
              <select class="form-control selectpicker" name="proveedor" id="cliente" required="" title="Seleccione" data-live-search="true" data-size="5" onchange="contacto(this.value);">
                @foreach($clientes as $client)
                  <option {{$factura->proveedor==$client->id?'selected':''}} value="{{$client->id}}">{{$client->nombre}} {{$client->apellido1}} {{$client->apellido2}} - {{$client->nit}}</option>
                @endforeach
              </select>
            </div>
          </div>
          <span class="help-block error">
            <strong>{{ $errors->first('productor') }}</strong>
          </span>
        </div>    
        <div class="form-group row">
          <label class="col-sm-4  col-form-label">Observaciones <br> <small>(No visible en el documento impreso)</small></label>
          <div class="col-sm-8">
            <textarea  class="form-control form-control-sm min_max_100" name="observaciones">{{$factura->observaciones}}</textarea>
          </div>
        </div>
        <div class="form-group row">
          <label class="col-sm-4  col-form-label">Notas <br> <small>(Visible en el documento impreso)</small></label>
          <div class="col-sm-8">
            <textarea  class="form-control form-control-sm min_max_100" name="notas">{{$factura->notas}}</textarea>
          </div>
        </div>

      </div> 
      <div class="col-md-6 offset-md-1">
        <div class="form-group row">
          <label class="col-sm-4 col-form-label">Número de factura</label>
          <div class="col-sm-8">
            <input type="text" class="form-control"  id="codigo" name="codigo" value="{{$factura->codigo}}" required="true" maxlength="35">
          </div>
        </div>
        <div class="form-group row">
          <label class="col-sm-4 col-form-label">Fecha <span class="text-danger">*</span></label>
          <div class="col-sm-8">
            <input type="text" class="form-control datepicker"  id="fecha" value="{{date('d-m-Y', strtotime($factura->fecha_factura))}}" name="fecha" disabled=""  >
          </div>
        </div>
        <div class="form-group row">
          <label class="col-sm-4 col-form-label">Comprador</label>
          <div class="col-sm-8">
            <select name="vendedor" id="vendedor" class="form-control selectpicker " title="Seleccione" data-live-search="true" data-size="5">
              @foreach($vendedores as $vendedor)
                <option value="{{$vendedor->id}}" {{$factura->vendedor==$vendedor->id?'selected':''}}>{{$vendedor->nombre}}</option>
              @endforeach
            </select>
          </div>
        </div>
        <div class="form-group row">
          <label class="col-sm-4 col-form-label">Fecha de Entrega <span class="text-danger">*</span></label>
          <div class="col-sm-8">
            <input type="text" class="form-control" id="vencimiento" value="{{date('d-m-Y', strtotime($factura->vencimiento_factura))}}" name="vencimiento" disabled="">
          </div>
        </div>

        <div class="form-group row">
          <label class="col-sm-4 col-form-label">Bodega <span class="text-danger">*</span></label>
          <div class="col-sm-8">
            <select name="bodega" id="bodega" class="form-control"  required="">
              @foreach($bodegas as $bodega)  
                <option value="{{$bodega->id}}" {{$factura->bodega==$bodega->id?'selected':''}}>{{$bodega->bodega}}</option>
              @endforeach
            </select>
          </div>
        </div>
      </div>
    </div>

	<div class="alert alert-warning nopadding onlymovil" style="text-align: center;">
				<button type="button" class="close" data-dismiss="alert">×</button>
				<strong><small><i class="fas fa-angle-double-left"></i> Deslice <i class="fas fa-angle-double-right"></i></small></strong>
			</div>
      <!-- Desgloce -->
      <div class="fact-table">
    <div class="row">
      <div class="col-md-12">
        <table class="table table-striped table-sm" id="table-form" width="100%">
          <thead class="thead-dark">
            <th width="28%">Categoría/Ítem</th>
            <th width="8%">Precio</th>
            <th width="5%">Desc %</th>
            <th width="12%">Impuesto</th>
            <th width="13%">Descripción</th>
            <th width="7%">Cantidad</th>
            <th width="10%">Total</th>
            <th width="2%"></th>
          </thead>
          <tbody>
            @php $cont=0; @endphp
            @foreach($items as $item) 
            @php $cont+=1; @endphp
            <tr id="{{$cont}}">                          
              <td  class="no-padding">
                  <div class="resp-item">
                  <input type="hidden" name="id_item{{$cont}}" value="{{$item->id}}">     
                <select class="form-control form-control-sm selectpicker no-padding"  title="Seleccione" data-live-search="true" data-size="5" name="item[]" id="item{{$cont}}" onchange="rellenar(1, this.value);" required="">
                <optgroup label="Ítems inventariables">
                @foreach($inventario as $itemm)
                <option value="{{$itemm->id}}" {{$item->producto==$itemm->id ?'selected':''}} >{{$itemm->producto}} - ({{$itemm->ref}})</option>
                 @endforeach
                </optgroup>
                <optgroup label="Puc">
                @foreach($categorias as $categoria)
                      <option  {{$item->producto==$categoria->id && $item->tipo_item==2?'selected':''}}  value="cat_{{$categoria->id}}" {{$categoria->estatus==0?'disabled':''}}>{{$categoria->nombre}}-({{$categoria->codigo}})</option>
                      @endforeach
                </optgroup>
                </select>
                  <p class="text-left nomargin">
                    <a href="" data-toggle="modal" data-target="#modalproduct" class="modalTr" tr="1">
                      <i class="fas fa-plus"></i> Nuevo Producto
                    </a>
                  </p>
              </div>
              </td>
              <td class="monetario">
                  <div class="resp-precio">
                <input type="number" class="form-control form-control-sm" id="precio{{$cont}}" name="precio[]" placeholder="Precio Unitario" onkeyup="total({{$cont}})" required=""  value="{{$item->precio}}">
                </div>
              </td>
              <td>
                <input type="text" class="form-control form-control-sm" id="desc{{$cont}}" name="desc[]" placeholder="%" onkeyup="total({{$cont}})" value="{{$item->desc}}">
              </td>
              <td>        
                <select class="form-control form-control-sm selectpicker" name="impuesto[]" id="impuesto{{$cont}}" title="Impuesto" onchange="totalall();" required="">
                  @foreach($impuestos as $impuesto)
                    <option value="{{$impuesto->id}}" porc="{{$impuesto->porcentaje}}" {{$item->id_impuesto==$impuesto->id?'selected':''}}>{{$impuesto->nombre}} - {{$impuesto->porcentaje}}%</option>
                  @endforeach
                </select>
              </td>
              <td  style="padding-top: 1% !important;"> 
              <div class="resp-descripcion">
                <textarea  class="form-control form-control-sm" id="descripcion{{$cont}}" name="descripcion[]" placeholder="Descripción" >{{$item->descripcion}}</textarea>
             </div>
              </td>
              <td width="5%">
                <input type="number" class="form-control form-control-sm" id="cant{{$cont}}" name="cant[]" placeholder="Cantidad" onchange="total(1);" min="1" required="" value="{{$item->cant}}">
              </td>
              <td>
                  <div class="resp-total">
                <input type="text" class="form-control form-control-sm text-right" id="total{{$cont}}" value="{{App\Funcion::Parsear($item->total())}}" disabled="">
              </div>
              </td>
              <td><button type="button" class="btn btn-outline-secondary btn-icons" onclick="Eliminar({{$cont}});">X</button></td>
            </tr>
             @endforeach
          </tbody>
        </table>
        <div class="alert alert-danger" style="display: none;" id="error-items"></div>
      </div>
    </div>

    <button class="btn btn-outline-primary" onclick="createRow();" type="button" style="margin-top: 5%">Agregar línea</button>
    <div class="row"  style="margin-top: 5%;">
          <div class="col-md-5 no-padding">
        <h5>RETENCIONES</h5>
            <table class="table table-striped table-sm" id="table-retencion">
              <thead class="thead-dark">
                <th width="60%">Tipo de Retención</th>
                <th width="34%">Valor</th>
                <th width="5%"></th>
              </thead>
              <tbody>

                @php $total=$cont=0; @endphp 
                @foreach($retencionesFacturas as $retenido)
                <tr  id="reten{{$cont}}"> 
                  <td  class="no-padding">                          
                    <select class="form-control form-control-sm selectpicker no-padding"  title="Seleccione" data-live-search="true" data-size="5" name="retencion[]" id="retencion{{$cont}}" required="" onchange="retencion_calculate({{$cont}}, this.value);" >

                        @foreach($retenciones as $retencion)
                                <option {{$retencion->id==$retenido->id_retencion?'selected':''}}  value="{{$retencion->id}}">{{$retencion->nombre}} ({{$retencion->porcentaje}}%)</option>
                        @endforeach
                    </select>
                  </td>
                  <td class="monetario">
                    <input type="hidden" name="reten{{$cont}}" value="{{$retenido->id}}">
                    <input type="hidden" value='0' id="lock_reten{{$cont}}">
                    <input type="monetario" style="display: inline-block; width: 80%;" class="form-control form-control-sm" onkeyup="total_categorias()" id="precio_reten{{$cont}}" name="precio_reten[]" placeholder="Valor retenido" onkeyup="total_linea({{$cont}})" required="" value="{{$retenido->valor}}" disabled="">
                  </td>
                  <td>
                    <button type="button" class="btn btn-outline-secondary btn-icons" onclick="Eliminar('reten{{$cont}}'); total_categorias();">X</button>
                  </td>
                </tr>
                @php $cont+=1; @endphp
                @endforeach
              </tbody>
            </table>
            <button class="btn btn-outline-primary" onclick="CrearFilaRetencion();" type="button" style="margin-top: 2%;">Agregar Retención</button>
          </div>
          <div class="col-md-7">
            <h5>FORMAS DE PAGO <a><i data-tippy-content="Elige a que cuenta ira enlazado el movimiento contable" class="icono far fa-question-circle"></i></a></h5>
            <table class="table table-striped table-sm" id="table-formaspago">
              <thead class="thead-dark">
                <th width="50%">Cuenta</th>
                <th width="25%">Cruce</th>
                <th width="20%" class="no-padding">Valor</th>
                <th width="5%"></th>
              </thead>
              <tbody>
                @php $cont=0; $totalformas= 0; @endphp
                  @foreach($formasPago as $forma) 
                @php $cont+=1; $totalformas+=$forma->credito; @endphp
                  <tr id="forma{{$cont}}" fila="{{$cont}}">
                    <td  class="no-padding">
                        <select class="form-control form-control-sm selectpicker no-padding"  title="Seleccione" data-live-search="true" data-size="5" name="formapago[]" id="formapago{{$cont}}" onchange="llenarSelectAnticipo(this.value, $factura->cliente);" required="" >
                            @if($forma->recibocaja_id != null)
                            <option value="0" selected>Agregar un anticipo</option>
                            @endif
                            @foreach($relaciones as $relacion)
                                <option value="{{$relacion->id}}" {{$relacion->id == $forma->formapago_id ? 'selected': ''}}>{{$relacion->codigo}} - {{$relacion->nombre}}</option>
                            @endforeach
                        </select>
                      </td>
                      <td  class="no-padding" id="tdanticipo{{$cont}}">
                          <select class="form-control form-control-sm selectpicker no-padding"  title="Seleccione" data-live-search="true" data-size="5" name="selectanticipo[]" id="selectanticipo{{$cont}}">
                            @if($forma->recibocaja_id != null)
                              @php $i = 1; @endphp
                              @foreach($factura->gastosAnticipo(1) as $recibo)
                                <option value="{{$recibo->id}}" id="optionAnticipo{{$i}}" precio="{{round($recibo->valor_anticipo,4)}}" {{$recibo->id == $forma->recibocaja_id ? 'selected': ''}}>EG-{{$recibo->nro}} - {{round($recibo->valor_anticipo,4)}}</option>
                              @php  $i++; @endphp
                              @endforeach
                            @endif
                          </select>
                      </td>
                      <td class="monetario">
                        <input type="hidden" value='0' id="lock_forma{{$cont}}">
                        <input type="number" required="" style="display: inline-block; width: 100%;" class="form-control form-control-sm"  value="{{$forma->credito}}" maxlength="24" id="precioformapago{{$cont}}" name="precioformapago[]" placeholder="valor forma de pago" onkeyup="total_linea_formapago({{$cont}})" required="" min="0">
                      </td>
                    <td><button type="button" class="btn btn-outline-secondary btn-icons" onclick="Eliminar_forma('forma{{$cont}}');">X</button></td>                          
                  </tr>
                  @endforeach
              </tbody>
            </table>
            <div class="row">
              <div class="col-md-6">
                <button class="btn btn-outline-primary" onclick="CrearFilaFormaPago();" type="button" style="margin-top: 2%;">Agregar forma de pago</button><a><i data-tippy-content="Agrega nuevas formas de pago haciendo <a href='#'>clíck aquí</a>" class="icono far fa-question-circle"></i></a>
              </div>
              <div class="col-md-6 d-flex justify-content-between pt-3">
                <h5>Total:</h5>
                <span>$</span><span id="anticipototal">{{$totalformas}}</span>  
              </div>
              <div class="col-md-12">
                <span class="text-danger" style="font-size:12px"><strong>El total de las formas de pago debe coincidir con el total neto</strong></span>
              </div>
            </div>
        </div>
    </div>
    <!-- Totales -->
        <div class="row" style="margin-top: 10%;">
          <div class="col-md-4 offset-md-8">
            <table class="text-right widthtotal">
              <tr>
                <td width="40%">Subtotal</td>
                <td>{{Auth::user()->empresa()->moneda}}<span id="subtotal">{{App\Funcion::Parsear($factura->total()->subtotal)}}</span></td>
                <input type="hidden" id="subtotal_categoria_js" value="{{App\Funcion::Parsear($factura->total()->subtotal)}}">
              </tr>
              <tr>
                <td>Descuento</td><td id="descuento">{{App\Funcion::Parsear($factura->total()->descuento)}}</td>
              </tr>
            </table>

            <table class="text-right widthtotal"  style="width: 100%" id="totales">
              <tr style="display: none">
                <td width="40%">Subtotal</td>
                <td>{{Auth::user()->empresa()->moneda}} <span id="subsub">{{App\Funcion::Parsear($factura->total()->subsub)}}</span></td>
              </tr>
              <tr >
                <td width="40%">Subtotal</td>
                <td>{{Auth::user()->empresa()->moneda}} <span id="subtotal2">{{App\Funcion::Parsear($factura->total()->subtotal -$factura->total()->descuento)}}</span></td>
              </tr>

              @php $cont=0; @endphp
              @if($factura->total()->imp)
                @foreach($factura->total()->imp as $imp)
                  @if(isset($imp->total)) @php $cont+=1; @endphp
                  <tr id="imp{{$cont}}">
                    <td>{{$imp->nombre}} ({{$imp->porcentaje}}%)</td>
                    <td id="totalimp{{$cont}}">{{Auth::user()->empresa()->moneda}}{{App\Funcion::Parsear($imp->total)}}</td>
                  </tr>
                  @endif
                @endforeach
              @endif
            </table>

            <table class="text-right widthtotal"  id="totalesreten" style="width: 100%">
              <tbody>
              @php $cont=0; @endphp
              @if($factura->total()->reten)
                @foreach($factura->total()->reten as $key => $reten)

                  @if(isset($reten->total))
                    @php $cont+=1; @endphp
                    <input type="hidden" id="retentotalmonto{{$reten->id}}" value="{{$reten->total}}">
                    <tr id="retentotal{{$key}}">
                      <td width="40%" >{{$reten->nombre}} ({{$reten->porcentaje}}%)</td>
                      <td id="retentotalvalue{{$reten->id}}">
                        -{{Auth::user()->empresa()->moneda}} {{App\Funcion::Parsear($reten->total)}}</td>
                    </tr>

                  @endif
                @endforeach
              @endif
              </tbody>
            </table>

            <hr>
            <table class="text-right widthtotal" style="font-size: 24px !important;">
              <tr>
                <td width="40%">TOTAL</td>
                <td>{{Auth::user()->empresa()->moneda}} <span id="total">{{App\Funcion::Parsear($factura->total()->total)}}</span></td>
              </tr>
            </table>
          </div>
        </div>
      <hr>
      <div class="row" >
        
        <div class="col-sm-12" style="text-align: right;  padding-top: 1%;">
          
          <a href="{{route('facturasp.index')}}" class="btn btn-outline-secondary">Cancelar</a>
          <button type="submit" class="btn btn-success">Guardar</button>
        </div>
      </div>


    </form>

  {{-- Modal Nuevo producto  --}}
  <div class="modal fade" id="modalproduct" role="dialog">
    <div class="modal-dialog modal-lg">
      <input type="hidden" id="trFila" value="0">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title"></h4>
        </div>
        <div class="modal-body">
          @include('inventario.modal.create')
        </div>

      </div>
    </div>
  </div>


  <input type="hidden" id="impuestos" value="{{json_encode($impuestos)}}">
  @foreach ($impuestos as $impuesto)
      <input type="hidden" id="hddn_imp_{{$impuesto->id}}" value="{{$impuesto->tipo}}">
    @endforeach
  <input type="hidden" id="allproductos" value="{{json_encode($inventario)}}">
  <input type="hidden" id="url" value="{{url('/')}}">
  <input type="hidden" id="jsonproduc" value="{{route('inventario.all')}}">
  <input type="hidden" id="simbolo" value="{{Auth::user()->empresa()->moneda}}">

  {{-- VARIABLE DE SALDO A FAVOR DEL CLIENTE --}}
  <input type="hidden" id="saldofavorcliente" name="saldofavorcliente">
  <input type="hidden" id="formaspago" value="{{json_encode($relaciones)}}">
  <input type="hidden" id="edit" value="1">
  <input type="hidden" id="factura" value="{{$factura->id}}">    

  <input type="hidden" id="allcategorias" value='
  <optgroup label="Puc">
                          @foreach($categorias as $categoria)
                                  <option {{old('categoria')==$categoria->id?'selected':''}} value="cat_{{$categoria->id}}" {{$categoria->estatus==0?'disabled':''}}>{{$categoria->nombre}}-{{$categoria->codigo}}</option>
                          @endforeach 
                        </optgroup>'>
  <input type="hidden" id="retenciones" value="{{json_encode($retenciones)}}">
  
  
          {{-- Modal Editar Direccion Contacto--}}
    <div class="modal fade" id="modaleditDirection" role="dialog"  data-backdrop="static" data-keyboard="false">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-header">
            <h4 class="modal-title">Editar Direcciones</h4>
          </div>
          <div class="modal-body">
        <div class="container">
          <div id="conte-modalesedit"></div>
        </div>
    </div>
    <div class="modal-footer">
    </div>
  </div>
</div>
</div>
{{-- /Modal Editar --}}

@endsection