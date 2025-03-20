<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Nombre extends Model
{
    use HasFactory;

    protected $connection = 'catcentral';// Conexión a 'catalogocentralizado'

    //Con esta instruccion se omite el ingreso de fechas en el modelo 
    public $timestamps = false;

    //Se asigna el nombre de la Tabla 
    protected $table = 'Nombre';

    //Se asigna el nombre del campo llave primaria
    protected $primaryKey='IdNombre';

    //Se asignan los campos que podran ser actualizados 
    protected $guarded = [];

    //optiene las relaciones de ascendentes
    public function padre()
    {
        return $this->belongsTo('App\Models\Nombre', 'IdNombreAscendente');
    }

    //Se indica la relacion de hijos de un taxon 
    public function hijos()
    {
        return $this->hasMany('App\Models\Nombre', 'IdNombreAscendente');
    }

    //Se indica la relacion de ascendente obliggatorio 
    public function ascendOblig()
    {
        return $this->belongsTo('App\Models\Nombre','IdAscendObligatorio');
    }

    //Se indica la relacion de hijos del ascendente obligatorio
    public function ascendObligHijos()
    {
        return $this->hasMany('App\Models\Nombre', 'IdAscendObligatorio');
    }

    //Se declara la relacion de uno a uno de las categorias 
    public function categoria()
    {
        return $this->belongsTo(CategoriasTaxonomicas::class, 'IdCategoriaTaxonomica');
    }

    //Se declara la relacion de uno a uno con la tabla scat 
    public function scat()
    {
        return $this->hasOne(Scat::class, 'IdNombre');
    }

    //Se declara la relacion de uno a muchos con la tabla Nombre_Relacion 
    public function nombreRel()
    {
        return $this->hasMany(Nombre_Relacion::class,'IdNombre');
    }
  
    public function relNombreAutor()
    {
        return $this->hasMany(RelNombreAutor::class, 'IdNombre');
    }

    public function relNombreCat()
    {
        return $this->hasMany(RelNombreCatalogo::class,'IdNombre');
    }

    public function relNombreRegion()
    {
        return $this->hasMany(RelNombreRegion::class,'IdNombre');
    }
  
    /*
    public function tipoRelacion(array $tiposRel){
        foreach($tiposRel as $rel){
           $desc = $rel->tipoRelacion->Descripcion;
        }
        return $desc;
    }
    */
    //Función para buscar por nombre
    public function scopeNombres($query, $nombres) {
    	if ($nombres) {
    		return $query->where('Nombre','like',"%$nombres%")
                         ->where('EstadoRegistro', '=', 1);
    	}
    }

    //Funación para buscar por idcat
    public function scopeFiltraArbol($query, $categ, $catalog)
    {
        if($catalog)
        {
            $query->whereIn('SCAT.IdGrupoSCAT', $catalog)
                  ->whereIn('Nombre.IdCategoriaTaxonomica',$categ)
                  ->where('EstadoRegistro', '=', 1)
                  ->OrderByRaw('CategoriaTaxonomica.IdNivel2 ASC, Nombre.NombreCompleto ASC, CategoriaTaxonomica.IdNivel1 ASC,  CategoriaTaxonomica.IdNivel3 ASC, 
                                      CategoriaTaxonomica.IdNivel4 ASC')
                  ->join('SCAT', 'SCAT.IdNombre', '=', 'Nombre.IdNombre')
                  ->join('CategoriaTaxonomica', 'CategoriaTaxonomica.IdCategoriaTaxonomica', '=', 'Nombre.IdCategoriaTaxonomica');
            return $query;
        }
    }
    
    //Funcion para buscar por taxon con filtro de categoria y catalogos
    public function scopeFiltraArbolTaxCat($query, $categ, $catalog, $taxon) {
    	if ($categ) {
            $query->whereIn('SCAT.IdGrupoSCAT', $catalog)
                  ->whereIn('Nombre.IdCategoriaTaxonomica',$categ)
                  ->whereRaw('LOWER(Nombre.NombreCompleto) LIKE LOWER(?)',["%$taxon%"])
                  ->where('EstadoRegistro', '=', 1)
                  ->OrderByRaw('CategoriaTaxonomica.IdNivel2 ASC, Nombre.NombreCompleto ASC, CategoriaTaxonomica.IdNivel1 ASC,  CategoriaTaxonomica.IdNivel3 ASC, 
                                      CategoriaTaxonomica.IdNivel4 ASC')
                  ->join('SCAT', 'SCAT.IdNombre', '=', 'Nombre.IdNombre')
                  ->join('CategoriaTaxonomica', 'CategoriaTaxonomica.IdCategoriaTaxonomica', '=', 'Nombre.IdCategoriaTaxonomica');
            return $query;
        }
    }

    public function scopeCargaHijos($query, $id) {
    	if ($id) {
            $query->where('Nombre.IdNombreAscendente', '=', $id)
                  ->where('Nombre.IdNombre','<>', $id)
                  ->where('Nombre.EstadoRegistro', '=', 1)
                  ->OrderByRaw('CategoriaTaxonomica.IdNivel2 ASC, Nombre.NombreCompleto ASC, CategoriaTaxonomica.IdNivel1 ASC,  CategoriaTaxonomica.IdNivel3 ASC, 
                                      CategoriaTaxonomica.IdNivel4 ASC')
                  ->join('CategoriaTaxonomica', 'CategoriaTaxonomica.IdCategoriaTaxonomica', '=', 'Nombre.IdCategoriaTaxonomica');
            return $query;
        }
    }

    public function scopeCargaRelaciones($query, $id)
    {
        if ($id) {
            $query->groupByRaw('Tipo_Relacion.Descripcion, Nombre.NombreCompleto, Nombre.Estatus')
                  ->selectRaw('Tipo_Relacion.Descripcion, Nombre.NombreCompleto, Nombre.Estatus, 
                                count(RelacionBibliografia.IdBibliografia) as Biblio')
                  ->where('Nombre_Relacion.IdNombre', '=', $id)
                  ->where('Nombre.EstadoRegistro', '=', 1)
                  ->OrderByRaw('Tipo_Relacion.IdTipoRelacion ASC, Nombre.NombreCompleto ASC')
                  ->join('Nombre_Relacion', 'Nombre.IdNombre', '=', 'Nombre_Relacion.IdNombreRel')
                  ->join('Tipo_Relacion', 'Tipo_Relacion.IdTipoRelacion','=', 'Nombre_Relacion.IdTipoRelacion')
                  ->join('RelacionBibliografia', function($join){
                                $join->on('RelacionBibliografia.IdNombre','=','Nombre_Relacion.IdNombre');
                                $join->on('RelacionBibliografia.IdNombreRel','=','Nombre_Relacion.IdNombreRel');
                                $join->on('RelacionBibliografia.IdTipoRelacion','=','Nombre_Relacion.IdTipoRelacion');
                  });
            return $query;
        }
    }

    public function scopeCargaReferencias($query, $id) {
    	if ($id) {
            $query->where('Nombre.IdNombre', '=', $id)
                  ->selectRaw('Bibliografia.IdBibliografia, Bibliografia.Autor, Bibliografia.Anio,
                               Bibliografia.TituloPublicacion AS Titulo, 
                               Bibliografia.CitaCompleta AS Cita')
                  ->OrderByRaw('Bibliografia.IdBibliografia, Bibliografia.Autor, Bibliografia.Anio,
                                Bibliografia.TituloPublicacion, Bibliografia.CitaCompleta')
                  ->join('RelNombreBiblio', 'RelNombreBiblio.IdNombre', '=', 'Nombre.IdNombre')
                  ->join('Bibliografia', 'Bibliografia.IdBibliografia', '=', 'RelNombreBiblio.IdBibliografia');
            return $query;
        }
    }
}
