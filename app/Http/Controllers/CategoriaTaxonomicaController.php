<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CategoriaTaxonomicaController extends Controller
{
    public function index()
    {
        // Aquí generas o recuperas los datos para la gráfica de árbol.
        $treeData = $this->generateTreeMapData(); // O recupera de la base de datos

        $barChartData = $this->generateBarChartData(); //datos de prueba barChart

        return inertia('CategoriaTaxonomica', [
            'treeData' => $treeData,
            'data' => $barChartData
        ]);
    }


    private function generateBarChartData() {
        return [
            ['Task', 'Hours per Day'],
            ['Work',     11],
            ['Eat',      2],
            ['Commute',  2],
            ['Watch TV', 2],
            ['Sleep',    7]
        ];
    }
    private function generateTreeMapData()
    {
        // Datos de ejemplo (¡reemplaza con tus datos reales!)
        return [
            ['Location', 'Parent', 'Market trade volume (size)', 'Market increase/decrease (color)'],
            ['Global', null, 0, 0],
            ['America', 'Global', 0, 0],
            ['Europe', 'Global', 0, 0],
            ['Asia', 'Global', 0, 0],
            ['Australia', 'Global', 0, 0],
            ['Africa', 'Global', 0, 0],
            ['Brazil', 'America', 11, 10],
            ['USA', 'America', 52, -5],
            ['Mexico', 'America', 31, 1],
            ['Canada', 'America', 12, -4],
            ['Germany', 'Europe', 11, -2],
            ['France', 'Europe', 16, 3],
            ['Italy', 'Europe', 15, -1],
            ['Spain', 'Europe', 12, 4],
            ['Russia', 'Europe', 8, -2],
            ['China', 'Asia', 36, 4],
            ['Japan', 'Asia', 20, -8],
            ['India', 'Asia', 40, 12],
            ['Indonesia', 'Asia', 15, -2],
            ['Australia', 'Australia', 25, 0],
            ['Egypt', 'Africa', 21, 0],
            ['South Africa', 'Africa', 12, 0]
        ];
    }
}