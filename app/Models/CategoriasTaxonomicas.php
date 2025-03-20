<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CategoriasTaxonomicas extends Model
{
    use HasFactory;

    protected $connection = 'catcentral';// Conexión a 'catalogocentralizado'

    //Con esta instruccion se omite el ingreso de fechas en el modelo 
    public $timestamps = false;

    //Se asigna el nombre de la Tabla 
    protected $table = 'CategoriaTaxonomica';

    //Se asigna el nombre del campo llave primaria
    protected $primaryKey='IdCategoriaTaxonomica';

      //Se asignan los campos que podran ser actualizados 
    protected $guarded = [];
    
    public function nombre()
    {
      return $this->belongsToMany(Nombre::class,'IdCategoriaTaxonomica');
    }

    //Función para cargar el combo de categorias en caso de nuevo taxon  
    public function scopeListaCat($query, $categoria){
      if($categoria->IdNivel4 === 0 && $categoria->IdNivel1 < 7)
      {
        $query->where([['IdNivel1', '=', $categoria->IdNivel1],
                       ['IdNivel2', '=', $categoria->IdNivel2],
                       ['IdNivel3', '>', $categoria->IdNivel3],
                       ['IdNivel4', '>=', $categoria->IdNivel4],
                       ['EstadoRegistro',true]])
              ->orWhere([['IdNivel1', '=', ($categoria->IdNivel1)+1],
                         ['IdNivel2', '=', $categoria->IdNivel2],
                         ['IdNivel3', '=', '0'],
                         ['EstadoRegistro',true]]);

      }
      else if($categoria->IdNivel4 === 0 && $categoria->IdNivel1 === 7)
      {
        $query->where([['IdNivel1', '=', $categoria->IdNivel1],
                       ['IdNivel2', '=', $categoria->IdNivel2],
                       ['IdNivel3', '>', $categoria->IdNivel3],
                       ['IdNivel4', '=', $categoria->IdNivel4],
                       ['EstadoRegistro',true]])
              ->orWhere([['IdNivel1', '=', ($categoria->IdNivel1)+1],
                         ['IdNivel2', '=', $categoria->IdNivel2],
                         ['IdNivel3', '=', '0'],
                         ['EstadoRegistro',true]]);
      }
      else
      {
        $query->where([['IdNivel1', '=', $categoria->IdNivel1],
                       ['IdNivel2', '=', $categoria->IdNivel2],
                       ['IdNivel3', '=', $categoria->IdNivel3],
                       ['IdNivel4', '>', $categoria->IdNivel4],
                       ['EstadoRegistro',true]])
              ->orWhere([['IdNivel1', '=', ($categoria->IdNivel1)+1],
                       ['IdNivel2', '=', $categoria->IdNivel2],
                       ['IdNivel3', '=', '0'],
                       ['EstadoRegistro',true]]);
      }
    }
}
