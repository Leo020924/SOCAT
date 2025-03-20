<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RelModuloRol extends Model
{
    use HasFactory;

    protected $connection = 'socat';

    //Se asigna el nombre de la Tabla 
    protected $table = 'relmodulorol';

    //Se asigna el nombre del campo llave primaria
    protected $primaryKey= null;

    public $incrementing = false; // Desactiva el incremento de clave primaria

    protected $fillable = ['IdRol', 'IdModulo', 'Altas', 'Bajas', 'Cambios', 'Visible'];

    public function rol()
    {
        return $this->belongsTo(rol::class, 'IdRol');
    }

    public function modulo()
    {
        return $this->belongsTo(modulosocat::class, 'IdModulo');
    }

}
