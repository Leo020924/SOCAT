<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TipoDistribucion extends Model
{
    use HasFactory;

    protected $connection = 'catcentral';// Conexión a 'catalogocentralizado'

    //Con esta instruccion se omite el ingreso de fechas en el modelo 
    public $timestamps = false;
    
    protected $table = 'TipoDistribucion';

    protected $primaryKey='IdTipoDistribucion';

    protected $fillable = [
        'Descripcion'
    ];

    //Se cambia el campo para realizar busquedas
    public function getRouteKeyName()
    {
        return 'Descripcion';
    }

}
