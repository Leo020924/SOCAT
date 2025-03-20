<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NomComun extends Model
{
    use HasFactory;

    protected $connection = 'catcentral';// Conexión a 'catalogocentralizado'

    //Con esta instruccion se omite el ingreso de fechas en el modelo 
    public $timestamps = false;

    //Se asigna el nombre de la Tabla 
    protected $table = 'NomComun';

    //Se asigna el nombre del campo llave primaria
    protected $primaryKey='IdNomComun';

    //Se asignan los campos que podran ser actualizados 
    protected $guarded = [];

    //Se cambia el campo para realizar busquedas
    public function getRouteKeyName()
    {
        return 'NomComun';
    }

   

     //Se crea la funcion para busqueda de Nombres comunes por nombre comun
     public function scopeNomComun($query, $nombre) {
    	if ($nombre) {
            return $query->where('NomComun','like',"%$nombre%")
                         ->where('EstadoRegistro', true);
    	}
    }

        //Se crea la funcion para busqueda de Nombres comunes por lengua
    public function scopeLengua($query, $lengua) {
    	if ($lengua) {
            return $query->where('Lengua','like',"%$lengua%")
                         ->where('EstadoRegistro', true);
    	}
    }

    //Se crea la funcion para busqueda de Nombres comunes por observaciones
    public function scopeObservacion($query, $observacion) {
    	if ($observacion) {
            return $query->where('Observaciones','like',"%$observacion%")
                         ->where('EstadoRegistro', true);
    	}
    }
}
