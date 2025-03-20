<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CategoriasTaxonomicas;
use App\Models\Nombre;
use Illuminate\Support\Facades\Log;

class CategoriasController extends Controller
{
 
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('catalogos.categoriasTaxonomicas.index'); 
    }

    public function fetchCatTax()
    {
        $catTax = CategoriasTaxonomicas::where('NombreCategoriaTaxonomica', '=', 'Reino')
                                           ->get();
        $categorias = [];
        
        $data=[
                'label' => $catTax[0]->NombreCategoriaTaxonomica,
                'id' => $catTax[0]->IdCategoriaTaxonomica,
                'children'=>[],
            ];
        
        array_push($categorias, $data);

        return response()->json($categorias);
    }

    public function fetchHijos($id)
    {
        Log::info("Este es el id que se busca: " . $id);
        $catTax = CategoriasTaxonomicas::where('IdAscendente', '=', $id)
                                       ->where('IdCategoriaTaxonomica', '<>', $id)
                                           ->OrderbyRaw("IdNivel1 ASC, IdNivel3 ASC, IdNivel4 ASC,
                                                         IdNivel5 ASC, IdNivel6 ASC, IdNivel7 ASC,
                                                         IdNivel8 ASC, IdNivel9 ASC, IdNivel10 ASC,
                                                         IdNivel11 ASC, IdNivel12 ASC")
                                           ->get();
        Log::info($catTax);
        $data=[];
        $comp = true;
        foreach($catTax as $cat)
        {
            if($cat->IdNivel3 === 0 && $cat->IdNivel1 === 1){
                $mostrar= true;
            }
            else{
                $mostrar = false; 
                if($cat->IdNivel3 === 0)
                {
                    $comp = false;
                }  
            }
            $newHijo = [ 'id' => $cat->IdCategoriaTaxonomica, 
                         'label' => $cat->NombreCategoriaTaxonomica, 
                         'children' => [],
                         'visible' => $mostrar,
                         'completo' => $comp];

            array_push($data, $newHijo);
        }

        return response()->json($data);
    }

    public function update(Request $request, $id)
    {
        $catTax = CategoriasTaxonomicas::find($id);
        $catTax->NombreCategoriaTaxonomica = $request->valor;
        $catTax->update();

       return response()->json([
           'status=>200',
           'message'=> "El registro se actualizo son exito",
       ]);
    }

    public function store(Request $request, $id)
    {
        $textNiv = "";
        $valMax = true;
        $catTaxP = CategoriasTaxonomicas::find($id);

        
        $nueCat = new CategoriasTaxonomicas;

        $maxId = CategoriasTaxonomicas::query();

        for($niv=1; $niv < 13; $niv++)
        {
            $textNiv = "IdNivel".$niv;
            if($catTaxP[$textNiv] > 0 || $textNiv === "IdNivel2")
            {
                $nueCat->$textNiv = $catTaxP[$textNiv];
                $maxId = $maxId->where($textNiv, "=", $catTaxP[$textNiv]);
            }
            else
            {
                if($valMax)
                {
                    $maxId = $maxId->max($textNiv); 
                    $nueCat->$textNiv = $maxId + 1;
                    $valMax = false;
                }
                else{
                    $nueCat->$textNiv = $catTaxP[$textNiv];
                }
            }
        }

        
        $nueCat->NombreCategoriaTaxonomica = $request->valor;
        $nueCat->IdAscendente = $id;
        $nueCat->save();

        return response()->json([
            'status=>200',
            'message'=> "El registro se dio de alta con exito",
        ]);

    }

    public function delete($id)
    {
        $nombres = Nombre::where('IdCategoriaTaxonomica','=', $id)->get();
        $catHij = CategoriasTaxonomicas::where('IdAscendente','=',$id)->get();
 
        if($nombres->count() > 0)
        {
            return response()->json([
            'status=>400',
            'message'=> "No se puede eliminar ya que tiene nombres relacionados",
            ]);
        }        
        else if($catHij->count() > 0){
            return response()->json([
                'status=>400',
                'message'=> "No se puede eliminar ya que tiene categorias inferiores",
                ]);
        }
        else{
           $catTax = CategoriasTaxonomicas::find($id);
           $catTax->delete();
        }

        return response()->json([
            'status=>200',
            'message'=> "La categoria se elimino con exito",
        ]);
    }

}
