<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ModuloSocat extends Model
{
    use HasFactory;

    protected $connection = 'socat';

    //Se asigna el nombre de la Tabla 
    protected $table = 'ModuloSocat';

    //Se asigna el nombre del campo llave primaria
    protected $primaryKey='IdModulo';

    public function relModuloRol()
    {
        return $this->hasMany(RelModuloRol::class, 'IdModulo');
    }
}