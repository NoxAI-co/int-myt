<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class VentasExternasAdjunto extends Model
{
    protected $table = "ventas_externas_adjuntos";
    
    protected $fillable = [
        'venta_externa_id',
        'nombre_archivo',
        'ruta_archivo',
        'tipo_documento'
    ];
    
    public function ventaExterna()
    {
        return $this->belongsTo(VentasExternas::class, 'venta_externa_id');
    }
}
