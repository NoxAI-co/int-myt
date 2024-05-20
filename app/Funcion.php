<?php

namespace App;
use Auth;

class Funcion
{
    public static function Parsear($valor){
    	return number_format($valor, Auth::user()->empresa()->precision, Auth::user()->empresa()->sep_dec, (Auth::user()->empresa()->sep_dec=='.'?',':'.'));

    }
    
    public static function ParsearAPI($valor, $id){
        $empresa = Empresa::find($id);
        return number_format($valor, $empresa->precision, $empresa->sep_dec, ($empresa->sep_dec=='.'?',':'.'));

    }

    public static function precision($valor){
        return round($valor, Auth::user()->empresa()->precision);
    }

    public static function precisionAPI($valor, $id){
        $empresa = Empresa::find($id);
        return round($valor, $empresa->precision);
    }

    /**
     * Metodo para la resta de fechas
     *
     */
    public static function diffDates($date1, $date2){
        $dateTime1 = new \DateTime($date1);
        $dateTime2 = new \DateTime($date2);

        $interval = $dateTime1->diff($dateTime2);

        $plus = $interval ->format('%R%');

        if($plus == "+"){
            return 0;
        }

        return $interval->days;
    }

    public static function generateRandomString($length = 10){
        return substr(str_shuffle("0123456789"), 0, $length);
    }
}
