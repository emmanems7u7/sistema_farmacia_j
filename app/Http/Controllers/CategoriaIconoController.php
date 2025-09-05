<?php

namespace App\Http\Controllers;

use App\Models\CategoriaIcono;
use App\Models\Categoria;
use Illuminate\Http\Request;
use Illuminate\Support\Str;



use Illuminate\Support\Facades\Http;


class CategoriaIconoController extends Controller
{
    public function generarIcono($id)
    {
        $categoria = Categoria::findOrFail($id);

      
        
            
            

    
       
    }
}

