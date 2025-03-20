<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rol extends Model
{
    use HasFactory;

    protected $connection = 'socat';

    //Se asigna el nombre de la Tabla 
    protected $table = 'Rol';

    //Se asigna el nombre del campo llave primaria
    protected $primaryKey='IdRol';

    protected $fillable = ['Perfil', 'Nombre_Perfil', 'Descripcion'];

    public function users()
    {
        return $this->hasMany(users::class, 'IdRol', 'IdRol');
    }
    
    public function relmodulo()
    {
        return $this->hasMany(relmodulorol::class, 'IdRol', 'IdRol');
    }

}