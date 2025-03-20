<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RelNombreCatalogo extends Model
{
    use HasFactory;

    protected $connection = 'catcentral';// Conexión a 'catalogocentralizado'

    //Con esta instruccion se omite el ingreso de fechas en el modelo 
  public $timestamps = false;

  //Se asigna el nombre de la Tabla 
  protected $table = 'RelNombreCatalogo';

  //Se asigna el nombre del campo llave primaria
    //protected $primaryKey = array('IdNombre', 'IdNombreRel', 'IdTipoRelacion', IdBibliografia );
    protected $primaryKey = 'IdNombre';

    //Se asignan los campos que podran ser actualizados 
    protected $guarded = [];
 
    //Se declara la relacion de uno a uno de los datos de catalogonombre 
    public function catalogonombre()
    {
        return $this->belongsToMany(CatalogoNombre::class, 'IdCatNombre');
    }

    public function nombre()
    {
        return $this->belongsToMany(Nombre::class, 'IdNombre');
    }
}
