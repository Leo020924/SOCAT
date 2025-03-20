<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AutorTaxon extends Model
{
    use HasFactory;

    protected $connection = 'catcentral';// Conexión a 'catalogocentralizado'

    //Con esta instruccion se omite el ingreso de fechas en el modelo 
    public $timestamps = false;

    //Se asigna el nombre de la Tabla 
    protected $table = 'AutorTaxon';

    //Se asigna el nombre del campo llave primaria
    protected $primaryKey='IdAutorTaxon';

      //Se asignan los campos que podran ser actualizados 
    protected $guarded = [];

        //Se crea la funcion para busqueda de autores por nombre
    public function scopeAutoridad($query, $nombre) {
    	if ($nombre) {
            return $query->where('NombreAutoridad','like',"%$nombre%")
                         ->where('EstadoRegistro', true);
    	}
    }

        //Se crea la funcion para busqueda de autores por grupo
    public function scopeGrupo($query, $grupo) {
    	if ($grupo) {
            return $query->where('GrupoTaxonomico','like',"%$grupo%")
                         ->where('EstadoRegistro', true);
    	}
    }

    //Se crea la funcion para busqueda de autores por nombre
    public function scopeNombre($query, $nombre) {
    	if ($nombre) {
            return $query->where('NombreCompleto','like',"%$nombre%")
                         ->where('EstadoRegistro', true);
    	}
    }
}