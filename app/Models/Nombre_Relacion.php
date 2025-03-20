<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Nombre_Relacion extends Model
{
    use HasFactory;

    protected $connection = 'catcentral';// Conexión a 'catalogocentralizado'

    //Con esta instruccion se omite el ingreso de fechas en el modelo 
    public $timestamps = false;

    //Se asigna el nombre de la Tabla 
    protected $table = 'Nombre_Relacion';

    //Se asigna el nombre del campo llave primaria
    //protected $primaryKey = array('IdNombre', 'IdNombreRel', 'IdTipoRelacion' );
    protected $primaryKey = 'IdNombre';

    //Se asignan los campos que podran ser actualizados 
    protected $guarded = [];

    //Se declara la relacion de uno a uno con la tabla Tipo_Relacion 
    public function tipoRelacion()
    {
        return $this->belongsTo(Tipo_Relacion::class, 'IdTipoRelacion');
    }

    //Se indica la relacion de ascendente obliggatorio 
    public function nombre()
    {
        return $this->belongsTo(Nombre::class,'IdNombre');
    }

    public function scopeTipoRelacion($query, $idNom)
    {
        $query->Nombre::join('Nombre_Relacion','Nombre_Relacion.IdNombre', '=','Nombre.IdNombre')
                ->join('Tipo_Relacion', 'Tipo_Relacion.IdTipoRelacion', '=', 'Nombre_Relacion.IdTipoRelacion')
                ->select('Tipo_Relacion.Descripcion')
                ->where('Nombre.IdNombre','=',$idNom)
                ->distinct()->get();
        return $query;
    }
}
