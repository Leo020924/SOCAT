<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CatalogoNombre extends Model
{
    use HasFactory;

    protected $connection = 'catcentral';// Conexión a 'catalogocentralizado'

    //Con esta instruccion se omite el ingreso de fechas en el modelo 
    public $timestamps = false;
    
    protected $table = 'CatalogoNombre';

    protected $primaryKey='IdCatNombre';

    public function relNombreCat()
  {
      return $this->hasMany(RelNombreCatalogo::class,'IdCatNombre');
  }

    //Se asignan los campos que podran ser actualizados 
    protected $guarded = [];

    //Se crea la funcion para busqueda por descripcion
    public function scopeDescripcion($query, $desc) {
    if ($desc) {
      return $query->where('Descripcion','like',"%$desc%")
                   ->where('EstadoRegistro', true);
          }
    }

    //Función para cargar la primera lista
    public function scopeLista($query) {
          
           $query->where('Nivel1','>','0')
                 ->where('Nivel2','=','0')
                 ->where('EstadoRegistro',true);
          return $query;      
    }

    //Función para buscar los descendientes del valor seleccionado
    public function scopeListaDet($query, $asc) {
          $ultimo= '';
          if($asc->Nivel1 != 0)
          {
                for($niv =1; $niv<=10; $niv++)
                {
                   $valor = 'Nivel'.$niv;
                      if($asc->$valor > 0)
                      {
                           $query->where($valor,'=',$asc->$valor);
                           $ultimo = $valor;
                      }
                      else
                      {
                          if($ultimo != '')
                          {
                           $query->where($valor,'>','0');
                           $ultimo = '';
                          }
                          else{
                           $query->where($valor,'=','0');
                          }
                      }
                }
                $query->where('EstadoRegistro',true);   
          }
    
         return $query;      
   }

   public function scopeBusqueda($query, $busca){
          $query->select('Descripcion')
                ->where('Descripcion', 'like', '%'.$busca.'%');
          return $query;
   }

   public function scopeBuscar($query, $desc){
         $query->where('Descripcion', '=', $desc);
         return $query;
   }

   public function scopeAscendente($query, $desc, $niv)
   {
          $query->select('Descripcion');

         for($niveles= 1; $niveles<= 10; $niveles++)
         {
                $valor= 'Nivel'.$niveles;
               if($niveles >= $niv)
               {
                $query->where($valor, '=', 0 );
               }
               else
               {
                $query->where($valor,'=',$desc->$valor);
               }
         }

         return$query;
   }

   public function scopeListaAnt($query, $act){
          $nivSup= false;
          $cont=0;
          for($niv = 10; $niv >= 1; $niv--)
          {
                $valor= 'Nivel'.$niv;
                if($act->$valor != 0 && $nivSup === false)
                {
                      if($cont === 0)
                      {
                           $query->where($valor,'=',0);
                           $cont++; 
                      }
                      else
                      {
                            $query->where($valor,'>',0);
                            $nivSup = true;
                      }
                }
                else
                {
                      $query->where($valor,'=', $act->$valor);
                }
          }
          $query->where('EstadoRegistro',true);

          return $query;
   }

   public function scopeActList($query, $act)
   {
          $nivSup= false;
          for($niv = 10; $niv >= 1; $niv--)
          {
                $valor= 'Nivel'.$niv;
                if($act->$valor != 0 && $nivSup === false)
                {
                      $query->where($valor,'>',0);
                      $nivSup = true;
                }
                else
                {
                      $query->where($valor,'=', $act->$valor);
                }
          }
          $query->where('EstadoRegistro',true);

          return $query;
    }

    public function scopeContDet($query, $asc) {
          $ultimo= '';
          $query->select(['catalogonombre.*']);
          if($asc->Nivel1 != 0)
          {
                for($niv =1; $niv<=10; $niv++)
                {
                   $valor = 'Nivel'.$niv;
                      if($asc->$valor > 0)
                      {
                           $query->where($valor,'=',$asc->$valor);
                           $ultimo = $valor;
                      }
                      else
                      {
                          if($ultimo != '')
                          {
                           $query->where($valor,'>','0');
                           $ultimo = '';
                          }
                          else{
                           $query->where($valor,'=','0');
                          }
                      }
                }
                $query->where('EstadoRegistro',true);   
          }
    
         return $query;      
   }
}
