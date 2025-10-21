<?php
namespace App\Repositories;

use App\Interfaces\CatalogoInterface;

use App\Models\Categoria;

use App\Models\Catalogo;
class CatalogoRepository extends BaseRepository implements CatalogoInterface
{

    public function __construct()
    {
        parent::__construct();

    }
    public function GuardarCatalogo($request)
    {
        $catalogo = Catalogo::create([
            'categoria_id' => $this->cleanHtml($request->input('categoria')),
            'catalogo_parent' => $this->cleanHtml($request->input('catalogo_parent')),
            'catalogo_codigo' => $this->cleanHtml($request->input('catalogo_codigo')),
            'catalogo_descripcion' => $this->cleanHtml($request->input('catalogo_descripcion')),
            'catalogo_estado' => $this->cleanHtml($request->input('catalogo_estado')),
            'accion_usuario' => auth()->user()->name ?? 'sistema',

        ]);
        return $catalogo;
    }

    public function EditarCatalogo($request, $catalogo)
    {

        $catalogo->update([
            'categoria_id' => $this->cleanHtml($request->categoria),
            'catalogo_parent' => $this->cleanHtml($request->catalogo_parent),
            'catalogo_codigo' => $this->cleanHtml($request->catalogo_codigo),
            'catalogo_descripcion' => $this->cleanHtml($request->catalogo_descripcion),
            'catalogo_estado' => $this->cleanHtml($request->catalogo_estado),
        ]);
    }

    public function GuardarCategoria($request)
    {
        $categoria = Categoria::create([
            'nombre' => $this->cleanHtml($request->input('nombre')),
            'descripcion' => $this->cleanHtml($request->input('descripcion')),
            'estado' => $this->cleanHtml($request->input('estado')),
        ]);
        return $categoria;
    }
    public function EditarCategoria($request, $categoria)
    {
        $categoria->update([
            'nombre' => $this->cleanHtml($request->input('nombre')),
            'descripcion' => $this->cleanHtml($request->input('descripcion')),
            'estado' => $this->cleanHtml($request->input('estado')),
        ]);

    }
}
