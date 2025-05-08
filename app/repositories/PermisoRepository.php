<?php
namespace App\Repositories;

use App\Interfaces\PermisoInterface;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\Menu;
use App\Models\Seccion;
class PermisoRepository extends BaseRepository implements PermisoInterface
{
    protected $permissions;

    public function __construct()
    {
        parent::__construct();
        $this->permissions = Permission::all();
    }
    public function GetPermisosTipo($tipo)
    {

        return $this->permissions->where('tipo', $tipo);
    }
    public function GetPermisoMenu($id, $rol_id)
    {
        $permission = Permission::findOrFail($id);

        $menus = Seccion::with('menus')->find($permission->id_relacion)->menus;

        if ($rol_id != -1) {
            $role = Role::find($rol_id);

        } else {
            $role = Role::all();

        }

        $permisos_menu = $permission->where('tipo', 'menu')->get();

        foreach ($permisos_menu as $permiso_menu) {
            foreach ($menus as $menu) {
                if ($permiso_menu->id_relacion == $menu->id) {

                    $permission = $permission->where('id_relacion', $menu->id)->where('tipo', 'menu')->first();

                    if ($rol_id != -1) {
                        if ($role->hasPermissionTo($permission)) {
                            $permission->check = true;
                        } else {
                            $permission->check = false;
                        }
                    }
                    $permisosPorTipo[] = $permission;

                }
            }

        }
        return $permisosPorTipo;
    }
    public function GetPermisoTipo($id, $tipo)
    {

    }
    function CrearPermiso($request)
    {
        Permission::create([
            'name' => $this->cleanHtml($request->name),
            'tipo' => 'permiso',
            'guard_name' => 'web',
        ]);

    }

    public function EditarPermiso($request, $permission)
    {

        $permission->update([
            'name' => $request->name,
        ]);

    }
}
