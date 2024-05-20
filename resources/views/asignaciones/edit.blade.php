@extends('layouts.app')

@section('boton')

@endsection

@section('content')
    <style>
        .readonly{ border: 0 !important; }
        .jay-signature-pad {
            position: relative;
            display: -ms-flexbox;
            -ms-flex-direction: column;
            width: 100%;
            height: 100%;
            max-width: 545px;
            max-height: 410px;
            border: 1px solid #e8e8e8;
            background-color: #fff;
            border-radius: 15px;
            padding: 20px;
            margin: 20px 0px;
        }
        .txt-center { text-align: -webkit-center; }
    </style>

    @if(Session::has('danger'))
        <div class="alert alert-danger" >
            {{Session::get('danger')}}
        </div>
        <script type="text/javascript">
            setTimeout(function(){
                $('.alert').hide();
                $('.active_table').attr('class', ' ');
            }, 50000);
        </script>
    @endif

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

    <form method="POST" action="{{ route('asignaciones.update', $contacto->id) }}" style="padding: 2% 3%;" role="form" class="forms-sample mt-0 pt-0" id="form-asignacion" enctype="multipart/form-data">
        {{ csrf_field() }}
        <input name="_method" type="hidden" value="PATCH">
        <div class="row">
            <div class="col-md-6 offset-md-3 p-3 mb-4 text-center" style="border: 1px solid {{ Auth::user()->empresa()->color }};border-radius: 0.25rem;font-size: .8em;background: #fff;">
                <b>CLIENTE</b><br>
                <span class="ml-2">{{ $contacto->nombre }} {{ $contacto->apellidos() }}</span>
                <br>
                <b>IDENTIFICACIÓN</b><br>
                <span class="ml-2">{{ $contacto->tip_iden('corta') }} {{ $contacto->nit }}</span>
                <br>
            </div>
            <div class="col-md-6 form-group d-none">
                <label class="control-label">Cliente <span class="text-danger">*</span></label>
                <div class="input-group">
                    <input type="file" class="form-control" id="id" name="id" value="{{$contacto->id}}">
                </div>
            </div>
            <div class="col-md-6 form-group">
                <label class="control-label" id="div_campo_1">{{$empresa->campo_1}}</label>
                <input type="file" class="form-control"  id="documento" name="documento" value="{{old('documento')}}" accept=".jpg, .jpeg, .png">
                <span style="color: red;">
                    <strong>{{ $errors->first('documento') }}</strong>
                </span>
            </div>
        </div>

        <div class="row">
            <div class="col-md-3 form-group">
                <label class="control-label" id="div_campo_a">{{$empresa->campo_a}}</label>
                <input type="file" class="form-control"  id="imgA" name="imgA"  value="{{old('imgA')}}" accept=".jpg, .jpeg, .png">
                <span style="color: red;">
                    <strong>{{ $errors->first('imgA') }}</strong>
                </span>
            </div>
            <div class="col-md-3 form-group">
                <label class="control-label" id="div_campo_b">{{$empresa->campo_b}}</label>
                <input type="file" class="form-control"  id="imgB" name="imgB"  value="{{old('imgB')}}" accept=".jpg, .jpeg, .png">
                <span style="color: red;">
                    <strong>{{ $errors->first('imgB') }}</strong>
                </span>
            </div>
            <div class="col-md-3 form-group">
                <label class="control-label" id="div_campo_c">{{$empresa->campo_c}}</label>
                <input type="file" class="form-control"  id="imgC" name="imgC"  value="{{old('imgC')}}" accept=".jpg, .jpeg, .png">
                <span style="color: red;">
                    <strong>{{ $errors->first('imgC') }}</strong>
                </span>
            </div>
            <div class="col-md-3 form-group">
                <label class="control-label" id="div_campo_d">{{$empresa->campo_d}}</label>
                <input type="file" class="form-control"  id="imgD" name="imgD"  value="{{old('imgD')}}" accept=".jpg, .jpeg, .png">
                <span style="color: red;">
                    <strong>{{ $errors->first('imgD') }}</strong>
                </span>
            </div>

            <div class="col-md-3 form-group">
                <label class="control-label" id="div_campo_e">{{$empresa->campo_e}}</label>
                <input type="file" class="form-control"  id="imgE" name="imgE"  value="{{old('imgE')}}" accept=".jpg, .jpeg, .png">
                <span style="color: red;">
                    <strong>{{ $errors->first('imgE') }}</strong>
                </span>
            </div>
            <div class="col-md-3 form-group">
                <label class="control-label" id="div_campo_f">{{$empresa->campo_f}}</label>
                <input type="file" class="form-control"  id="imgF" name="imgF"  value="{{old('imgF')}}" accept=".jpg, .jpeg, .png">
                <span style="color: red;">
                    <strong>{{ $errors->first('imgF') }}</strong>
                </span>
            </div>
            <div class="col-md-3 form-group">
                <label class="control-label" id="div_campo_g">{{$empresa->campo_g}}</label>
                <input type="file" class="form-control"  id="imgG" name="imgG"  value="{{old('imgG')}}" accept=".jpg, .jpeg, .png">
                <span style="color: red;">
                    <strong>{{ $errors->first('imgG') }}</strong>
                </span>
            </div>
            <div class="col-md-3 form-group">
                <label class="control-label" id="div_campo_h">{{$empresa->campo_h}}</label>
                <input type="file" class="form-control"  id="imgH" name="imgH"  value="{{old('imgH')}}" accept=".jpg, .jpeg, .png">
                <span style="color: red;">
                    <strong>{{ $errors->first('imgH') }}</strong>
                </span>
            </div>
            {{-- <div class="col-md-3 form-group">
                <label class="control-label">Plan <span class="text-danger">*</span></label>
                <div class="input-group">
                    <select class="form-control selectpicker" name="plan" id="plan" required="" title="Seleccione" data-live-search="true" data-size="5" value="{{ old($contrato->plan_id) }}">
                        @foreach($planes as $plan)
                            <option value="{{$plan->id}}" {{$plan->id==$contrato->plan_id?'selected':''}}>{{$plan->name}}</option>
                        @endforeach
                    </select>
                    <input type="hidden" id="servidor" value="{{old('plan')}}">
                </div>
                <span class="help-block error">
                    <strong>{{ $errors->first('plan') }}</strong>
                </span>
            </div>
            <div class="col-md-3 form-group" id="div_meses">
                <label class="control-label">Meses del contrato de permanencia</span></label>
                <div class="input-group">
                    <select class="form-control selectpicker" id="contrato_permanencia_meses" name="contrato_permanencia_meses" title="Seleccione" data-live-search="true" data-size="5">
                        <option value="3" {{3==$contrato->contrato_permanencia_meses?'selected':''}}>3 meses</option>
                        <option value="6" {{6==$contrato->contrato_permanencia_meses?'selected':''}}>6 meses</option>
                        <option value="9" {{9==$contrato->contrato_permanencia_meses?'selected':''}}>9 meses</option>
                        <option value="12" {{12==$contrato->contrato_permanencia_meses?'selected':''}}>12 meses</option>
                    </select>
                </div>
                <span class="help-block error">
                    <strong>{{ $errors->first('contrato_permanencia_meses') }}</strong>
                </span>
            </div> --}}
        </div>

        <center>
            <div id="signature-pad" class="jay-signature-pad">
                <div class="jay-signature-pad--body">
                    <canvas id="jay-signature-pad" style="border: 1px solid #333;margin-bottom: 5px;border-radius: 10px;width: 100%;height: 280px;"></canvas>
                </div>
                <div class="signature-pad--footer txt-center">
                    <div class="signature-pad--actions txt-center">
                        <div>
                            <button type="button" class="button clear btn btn-warning" data-action="clear">Limpiar</button>
                            <button type="button" class="button" data-action="change-color" style="display: none;">Change color</button>
                            <button class="btn btn-success d-none" data-action="save-png" id="btnFirma">Guardar</button>
                        </div>
                        <div>
                            <input type="hidden" id="dataImg" name="firma_isp">
                        </div>
                    </div>
                </div>
            </div>
        </center>

        <small>Los campos marcados con <span class="text-danger">*</span> son obligatorios</small>

        <hr>

        <div class="row">
            <div class="col-sm-12" style="text-align: right;  padding-top: 1%;">
                <a href="{{route('asignaciones.index')}}" class="btn btn-outline-secondary">Cancelar</a>
                <button type="submit" id="submitcheck" onclick="submitLimit(this.id)" class="btn btn-success d-none">Guardar</button>
                <button class="btn btn-success" onclick="btn_signature()">Guardar</button>
            </div>
        </div>
    </form>

    <script src="{{asset('vendors/signature_pad/2.3.2/signature_pad.min.js')}}"></script>
    <script src="{{asset('vendors/signature_pad/1.5.3/signature_pad.min.js')}}"></script>

    <script>
        var wrapper = document.getElementById("signature-pad");
        var clearButton = wrapper.querySelector("[data-action=clear]");
        var changeColorButton = wrapper.querySelector("[data-action=change-color]");
        var savePNGButton = wrapper.querySelector("[data-action=save-png]");
        // var saveJPGButton = wrapper.querySelector("[data-action=save-jpg]");
        // var saveSVGButton = wrapper.querySelector("[data-action=save-svg]");
        var canvas = wrapper.querySelector("canvas");
        var signaturePad = new SignaturePad(canvas, {
            backgroundColor: 'rgb(255, 255, 255)'
        });
        function resizeCanvas() {
            var ratio =  Math.max(window.devicePixelRatio || 1, 1);
            canvas.width = canvas.offsetWidth * ratio;
            canvas.height = canvas.offsetHeight * ratio;
            canvas.getContext("2d").scale(ratio, ratio);
            signaturePad.clear();
        }
        window.onresize = resizeCanvas;
        resizeCanvas();
        function download(dataURL, filename) {
            var blob = dataURLToBlob(dataURL);
            var url = window.URL.createObjectURL(blob);
            var a = document.createElement("a");
            a.style = "display: none";
            a.href = url;
            a.download = filename;
            document.body.appendChild(a);
            //a.click();
            window.URL.revokeObjectURL(url);
        }
        function dataURLToBlob(dataURL) {
            var parts = dataURL.split(';base64,');
            var contentType = parts[0].split(":")[1];
            var raw = window.atob(parts[1]);
            var rawLength = raw.length;
            var uInt8Array = new Uint8Array(rawLength);
            for (var i = 0; i < rawLength; ++i) {
                uInt8Array[i] = raw.charCodeAt(i);
            }
            return new Blob([uInt8Array], { type: contentType });
        }
        clearButton.addEventListener("click", function (event) {
            signaturePad.clear();
        });
        changeColorButton.addEventListener("click", function (event) {
            var r = Math.round(Math.random() * 255);
            var g = Math.round(Math.random() * 255);
            var b = Math.round(Math.random() * 255);
            var color = "rgb(" + r + "," + g + "," + b +")";
            signaturePad.penColor = color;
        });
        savePNGButton.addEventListener("click", function (event) {
            if (signaturePad.isEmpty()) {
                //alert("Ingrese la firma del cliente.");
                return false;
            } else {
                var dataURL = signaturePad.toDataURL();
                document.getElementById("dataImg").value = dataURL.replace(/^data:image\/png;base64,/, "+");
                console.log(dataImg);
                //document.getElementById("submitcheck").click();
            }
        });
    </script>
@endsection

@section('scripts')
    <script>
        function btn_signature(){
            document.getElementById("btnFirma").click();
        }
    </script>
@endsection
