<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RelNombreRegion extends Model
{
    use HasFactory;

    protected $connection = 'catcentral';// Conexión a 'catalogocentralizado'

    //Con esta instruccion se omite el ingreso de fechas en el modelo 
    public $timestamps = false;

    //Se asigna el nombre de la Tabla 
    protected $table = 'RelNombreRegion';

    //Se asigna el nombre del campo llave primaria
    protected $primaryKey='IdNombre';
}
