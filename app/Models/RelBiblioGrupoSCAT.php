<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RelBiblioGrupoSCAT extends Model
{
    use HasFactory;

    protected $connection = 'catcentral';

    //Con esta instruccion se omite el ingreso de fechas en el modelo 
    public $timestamps = false;

    //Se asigna el nombre de la Tabla 
    protected $table = 'RelBiblioGrupoSCAT';

    //Se asigna el nombre del campo llave primaria
    protected $primaryKey = 'IdBibliografia';

    //Se asignan los campos que podran ser actualizados
    //protected $guarded = [];

    //Se declara la relación de uno a muchos de los datos de GrupoScat 
    public function scat()
    {
        return $this->belongsToMany(GrupoScat::class, 'IdGrupoSCAT');
    }

    //Se declara la relación de uno a muchos de los datos de bibliografia 
    public function bibliografia()
    {
        return $this->belongsToMany(Bibliografia::class, 'IdBibliografia');
    }

    //Función para buscar por idcat
    public function scopeGrupoBiblio($query, $IdBiblio)
    {
        $query->selectRaw('GrupoSCAT.GrupoSCAT, RelBiblioGrupoSCAT.Observaciones, 
                           RelBiblioGrupoSCAT.IdGrupoSCAT, RelBiblioGrupoSCAT.IdBibliografia')
              ->where('RelBiblioGrupoSCAT.IdBibliografia', '=', $IdBiblio)
              ->join('GrupoSCAT','GrupoSCAT.IdGrupoSCAT','=','RelBiblioGrupoSCAT.IdGrupoSCAT');
      
      return $query;
    }
}
