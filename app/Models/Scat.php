<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Scat extends Model
{
    use HasFactory;

    protected $connection = 'catcentral';// Conexión a 'catalogocentralizado'

    //Con esta instruccion se omite el ingreso de fechas en el modelo 
    public $timestamps = false;

    //Se asigna el nombre de la Tabla 
    protected $table = 'SCAT';

    //Se asigna el nombre del campo llave primaria
    protected $primaryKey=['IdNombre', 'IDCAT'];

    //Se asignan los campos que podran ser actualizados 
    protected $guarded = [];

    public $incrementing = false;

    //Se declara la relacion de uno a uno de los grupo scat 
    public function grupoScat()
    {
        return $this->belongsTo(GrupoScat::class, 'IdGrupoSCAT');
    }

    //Se declara la relacion de uno a uno de los grupo scat 
    public function nombre()
    {
        return $this->belongsTo(Nombre::class, 'IdNombre');
    }
}
