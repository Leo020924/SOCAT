<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tipo_Relacion extends Model
{
    use HasFactory;

    protected $connection = 'catcentral';// Conexión a 'catalogocentralizado'

    //Con esta instruccion se omite el ingreso de fechas en el modelo 
    public $timestamps = false;
    
    protected $table = 'Tipo_Relacion';

    protected $primaryKey='IdTipoRelacion';

    //Se asignan los campos que podran ser actualizados 
    protected $guarded = [];

    //Se declara la relacion de uno a uno con la tabla Tipo_Relacion 
  public function nombreRel()
  {
      return $this->belongsToMany(Nombre_Relacion::class,'IdTipoRelacion');
  }

  public function scopeNombresRel($query, $ids)
  {
      if ($ids) {
          $query->where('Tipo_Relacion.IdTipoRelacion', '=', $ids)
                  ->join('Nombre_Relacion', 'Tipo_Relacion.IdTipoRelacion', '=', 'Nombre_Relacion.IdTipoRelacion')
                  ->selectRaw('count(*) as conteo');
          return $query;
      }
  }

  public function scopeBuscar($query, $desc){
      $query->where('IdTipoRelacion', '=', $desc);
      return $query;
  }

}
