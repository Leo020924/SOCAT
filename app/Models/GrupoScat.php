<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GrupoScat extends Model
{
    use HasFactory;

    protected $connection = 'catcentral';// Conexión a 'catalogocentralizado'

    //Con esta instruccion se omite el ingreso de fechas en el modelo 
    public $timestamps = false;

    //Se asigna el nombre de la Tabla 
    protected $table = 'GrupoSCAT';

    //Se asigna el nombre del campo llave primaria
    protected $primaryKey='IdGrupoSCAT';

      //Se asignan los campos que podran ser actualizados 
    protected $guarded = [];

    /*
    public function scat()
    {
        return $this->belongsToMany(Scat::class, 'IdGrupoSCAT');
    }*/

}
