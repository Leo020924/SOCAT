<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bibliografia extends Model
{
    use HasFactory;

    protected $connection = 'catcentral';// Conexión a 'catalogocentralizado'

        //Con esta instruccion se omite el ingreso de fechas en el modelo 
        public $timestamps = false;

        protected $table = 'Bibliografia';
        
        protected $primaryKey='IdBibliografia';
        
        //Se asignan los campos que podran ser actualizados 
        protected $guarded = [];
    
        public function relbibliografia()
        {
          return $this->belongsTo(RelacionBibliografia::class,'IdBibliografia');
        }
        
        public function relbibliogruposcat()
        {
          return $this->hasMany(RelBiblioGrupoSCAT::class, 'IdBibliografia');
        }
        
        public function relnombrebiblio()
        {
            return $this->hasMany(RelNombreBiblio::class, 'IdBibliografia');
        }
        
        public function relnombrecatbiblio()
        {
            return $this->hasMany(RelNombreCatalogoBiblio::class, 'IdBibliografia');
        }
        
        public function relnombrecatregbiblio()
        {
            return $this->hasMany(RelNombreCatalogoRegionBiblio::class, 'IdBibliografia');
        }
        
        public function relnombreregbiblio()
        {
            return $this->hasMany(RelNombreRegionBiblio::class, 'IdBibliografia');
        }
    
        public function relnomnomcomunregbiblio()
        {
            return $this->hasMany(RelNomNomComunRegionBiblio::class, 'IdBibliografia');
        }
        
        //Función para buscar 
        public function scopeBuscarBiblio($query, $texto, $opciones)
        {
          foreach($opciones as $busca)
          {
            switch ($busca) {
              case "Autor(es)":
                  if($query === '')
                  {
                    $query->where('Autor', 'LIKE', "$texto%");
                  }
                  else{
                    $query->orWhere('Autor', 'LIKE', "$texto%");
                  }
                  break;
              case "Año(s)":
                  if($query === '')
                  {
                    $query->where('Anio', 'LIKE', "$texto%");
                  }
                  else{
                    $query->orWhere('Anio', 'LIKE', "$texto%");
                  }
                  break;
              case "Titulo de la publicación":
                  if($query === '')
                  {
                    $query->where('TituloPublicacion', 'LIKE', "$texto%");
                  }
                  else{
                    $query->orWhere('TituloPublicacion', 'LIKE', "$texto%");
                  }
                  break;
              case "Cita bibliográfica":
                  if($query === '')
                  {
                    $query->where('CitaCompleta', 'LIKE', "$texto%");
                  }
                  else{
                    $query->orWhere('CitaCompleta', 'LIKE', "$texto%");
                  }
                  break;
              case "Titulo de la subpublicación":
                  if($query === '')
                  {
                    $query->where('TituloSubPublicacion', 'LIKE', "$texto%");
                  }
                  else{
                    $query->orWhere('TituloSubPublicacion', 'LIKE', "$texto%");
                  }
                  break;
              case "ISBN/ISSN":
                  if($query === '')
                  {
                    $query->where('ISBNISSN', 'LIKE', "$texto%");
                  }
                  else{
                    $query->orWhere('ISBNISSN', 'LIKE', "$texto%");
                  }
              break;
            }
          }  
          return $query;
        }
}
