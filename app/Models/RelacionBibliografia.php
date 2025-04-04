<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RelacionBibliografia extends Model
{
    use HasFactory;

    protected $connection = 'mysql';

    //Con esta instruccion se omite el ingreso de fechas en el modelo 
    public $timestamps = false;

    //Se asigna el nombre de la Tabla 
    protected $table = 'RelacionBibliografia';

    //Se asigna el nombre del campo llave primaria
    //protected $primaryKey = array('IdNombre', 'IdNombreRel', 'IdTipoRelacion', IdBibliografia );
    protected $primaryKey = 'IdBibliografia';

    //Se asignan los campos que podran ser actualizados 
    protected $guarded = [];

    //Se declara la relacion de uno a uno de las categorias 
    public function bibliografia()
    {
        return $this->belongsToMany(Bibliografia::class, 'IdBibliografia');
    }

    /*En Eloquent no se puede agregar las relaciones por llaves compuestas por lo que
    se esta resolviendo por medio de scope en los cuales se declaran los join explicitos*/
    /*public function scopeNombreRelacion($query, $nombre)
    {
        return $this->belongsTo(Tipo_Relacion::class, 'IdTipoRelacion');
    }*/
}
