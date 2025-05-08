<?php
namespace App\Repositories;

use App\Interfaces\MenuInterface;
use Illuminate\Support\Facades\Auth;
use App\Models\Menu;
use Spatie\Permission\Models\Permission;
use App\Models\Seccion;
class MenuRepository extends BaseRepository implements MenuInterface
{

    public function __construct()
    {
        parent::__construct();

    }
    public function CrearMenu($request)
    {

        $menu = Menu::create([
            'nombre' => $this->cleanHtml($request->input('nombre')),
            'orden' => $this->cleanHtml($request->input('orden', 0)),
            'padre_id' => $this->cleanHtml($request->input('padre_id')) ?: null,
            'seccion_id' => $this->cleanHtml($request->input('seccion_id')),
            'ruta' => $this->cleanHtml($request->input('ruta')),
            'accion_usuario' => Auth::user()->na
        ]);

        Permission::create([
            'name' => $menu->nombre,
            'tipo' => 'menu',
            'id_relacion' => $menu->id
        ]);
    }
    public function CrearSeccion($request)
    {
        $seccion = Seccion::create(
            [
                'titulo' => $this->cleanHtml($request->input('titulo')),
                'icono' => $this->cleanHtml($request->input('icono')),
                'accion_usuario' => Auth::user()->name,
            ]
        );
        Permission::create([
            'name' => $seccion->titulo,
            'tipo' => 'seccion',
            'id_relacion' => $seccion->id
        ]);
    }
}
