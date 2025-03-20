<?php

namespace App\Helpers;

use App\Models\CatalogoNombre;

class Helpers{

//Función es para mostrar con color la seccion seleccionada
    public static  function setActive($nombreRuta)
    {
        return request()->routeIs($nombreRuta) ? 'active' : '';
    }

    //Función que genera el nombre completo para los taxones en base a las reglas la TTN
    public static function taxonCompleto($nombre)
    {
        if($nombre->categoria->IdNivel1 < 7)
            {
                return $nombre->Nombre;
            }
        else 
            {
                $taxonCompleto = "";

                do{
                    switch ($nombre->categoria->NombreCategoriaTaxonomica) {
                        case "forma":
                                $taxonCompleto = "f. ". $nombre->Nombre . $taxonCompleto;
                            break;
                        case "raza":
                                $taxonCompleto = "r. ". $nombre->Nombre . $taxonCompleto;
                            break;
                        case "subespecie":
                                $taxonCompleto = "subsp. ". $nombre->Nombre . $taxonCompleto;
                            break;
                        case "subforma":
                                $taxonCompleto = "subf. ". $nombre->Nombre . $taxonCompleto;
                            break;
                        case "subvariedad":
                                $taxonCompleto = "subvar. ". $nombre->Nombre . $taxonCompleto;
                        break;
                        case "variedad":
                                $taxonCompleto = "var. ". $nombre->Nombre . $taxonCompleto;
                        break;
                        default:
                            if($nombre->categoria->IdNivel1 === 6 && $nombre->categoria->IdNivel3 > 0 )
                            {
                                $taxonCompleto = "(". $nombre->Nombre . ") " . $taxonCompleto; 
                            }
                            else
                            {
                                $taxonCompleto = $nombre->Nombre . " " . $taxonCompleto;
                            }
                        break;
                    }
                    $nombre = $nombre->padre;
                }while($nombre->categoria->IdNivel1 >= 6);
                
                return $taxonCompleto;
            }
    }

    //Función que genera el nombre completo para los taxones en base a las reglas de biotica
    public static function nombreCompleto($nombre)
    {
        if($nombre->categoria->IdNivel1 < 7)
            {
                return $nombre->Nombre;
            }
        else 
            {
                $taxonCompleto = "";
                if( $nombre->categoria->IdNivel2 === 0 )
                {
                    do{
                        switch ($nombre->categoria->NombreCategoriaTaxonomica) {
                            case "forma":
                                    $taxonCompleto = "f. ". $nombre->Nombre . $taxonCompleto;
                                break;
                            case "raza":
                                    $taxonCompleto = "r. ". $nombre->Nombre . $taxonCompleto;
                                break;
                            case "subespecie":
                                    $taxonCompleto = "subsp. ". $nombre->Nombre . $taxonCompleto;
                                break;
                            case "subforma":
                                    $taxonCompleto = "subf. ". $nombre->Nombre . $taxonCompleto;
                                break;
                            case "subvariedad":
                                    $taxonCompleto = "subvar. ". $nombre->Nombre . $taxonCompleto;
                            break;
                            case "variedad":
                                    $taxonCompleto = "var. ". $nombre->Nombre . $taxonCompleto;
                            break;
                            default:
                                if($nombre->categoria->IdNivel1 === 6 && $nombre->categoria->IdNivel3 > 0 )
                                {
                                    $taxonCompleto = "(". $nombre->Nombre . ") " . $taxonCompleto; 
                                }
                                else
                                {
                                    $taxonCompleto = $nombre->Nombre . " " . $taxonCompleto;
                                }
                            break;
                        }
                        $nombre = $nombre->padre;
                    }while($nombre->categoria->IdNivel1 >= 6);
                }
                else
                {
                    do{

                        if($nombre->categoria->IdNivel1 === 6 && $nombre->categoria->IdNivel3 > 0 )
                        {
                            $taxonCompleto = "(". $nombre->Nombre . ") " . $taxonCompleto; 
                        }
                        else
                        {
                            $taxonCompleto = $nombre->Nombre . " " . $taxonCompleto;
                        }

                        $nombre = $nombre->padre;

                    }while($nombre->categoria->IdNivel1 >= 6);
                }

                return $taxonCompleto;
            }
    }

    //Funcion para generar la lista de ascendentes 
    public static function listaAscen($nombre)
    {
        $ascendentes= "";

        do{
            $ascendentes = $nombre->IdNombre . ", " . $ascendentes;
            $nombre = $nombre->padre;
        }while($nombre->IdNombre != $nombre->padre->IdNombre);

        $ascendentes = $nombre->IdNombre . ", " . $ascendentes;

        return $ascendentes;
    }

    //Funcion para generar la lista de ascendentes obligatorios 
    public static function listaAscenOblig($nombre)
    {

        $ascenOblg= $nombre->IdNombre;
        $idAsc=0;
        do{

            if($nombre->IdAscendObligatorio != $idAsc)
            {
                $ascenOblg = $nombre->IdAscendObligatorio . ", " . $ascenOblg;
            }
            $idAsc = $nombre->IdAscendObligatorio;

            $nombre = $nombre->padre;

        }while($nombre->IdNombre != $nombre->padre->IdNombre);

        return $ascenOblg;
    }

    public static function ascCatNombre($catNom)
    {
        //return $catNom->Nivel1;
        $listaAscen= '';

        for($niv = 10; $niv>1; $niv--)
        {
            $valor= 'Nivel'.$niv;
            if($catNom->$valor > 0)
            {
                $valAsc= CatalogoNombre::Ascendente($catNom, $niv)
                         ->get();

                //return $valAsc[0]['Descripcion'];
                if($listaAscen != '' && $valAsc != null)
                {
                    $listaAscen= $valAsc[0]['Descripcion'].'->'.$listaAscen;
                }
                else
                {
                    $listaAscen = $valAsc[0]['Descripcion'];
                }
            }
        }
        return $listaAscen;
    }

    public static function actualizaHijos($nombre){

        foreach ($nombre->hijos as $hijo){

            $ascen = listaAscen($hijo);
            $ascenOblig = listaAscenOblig($hijo);
            $nomComp = nombreCompleto($hijo);
            $taxonComp = taxonCompleto($hijo);    

            return $nomComp;

            $hijo->update([
                    'Ascendentes' => $ascen,
                    'AscendentesObligatorios' => $ascenOblig, 
                    'NombreCompleto' => $nomComp,
                    'TaxonCompleto' => $taxonComp
            ]);

            //self::actualizaHijos($hijo);
        }
    }

}