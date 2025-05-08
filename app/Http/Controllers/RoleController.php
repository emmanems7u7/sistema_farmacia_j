<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Interfaces\RoleInterface;
use App\Interfaces\PermisoInterface;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
class RoleController extends Controller
{

    protected $RoleRepository;
    protected $PermisoRepository;
    public function __construct(RoleInterface $roleInterface, PermisoInterface $PermisoInterface)
    {
        $this->RoleRepository = $roleInterface;
        $this->PermisoRepository = $PermisoInterface;
    }

    public function index()
    {
        $roles = Role::all();
        $permissions = Permission::all();

        $breadcrumb = [
            ['name' => 'Inicio', 'url' => route('home')],
            ['name' => 'Roles', 'url' => route('roles.index')],
        ];
        return view('roles.index', compact('roles', 'breadcrumb', 'permissions'));
    }


    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:roles,name',
            'permissions' => 'array',
        ]);

        $this->RoleRepository->CrearRol($request);

        return redirect()->route('roles.index')->with('success', 'Rol creado correctamente.');
    }
    public function create()
    {

        $role = Role::all()->load('permissions');

        $permisosPorTipo = [
            'permiso' => $this->PermisoRepository->GetPermisosTipo('permiso'),
            'seccion' => $this->PermisoRepository->GetPermisosTipo('seccion'),
        ];

        $breadcrumb = [
            ['name' => 'Inicio', 'url' => route('home')],
            ['name' => 'Roles', 'url' => route('roles.index')],
        ];

        return view('roles.create', compact('role', 'permisosPorTipo', 'breadcrumb'));
    }

    public function edit($id)
    {

        $role = Role::findOrFail($id)->load('permissions');


        $permisosPorTipo = [
            'permiso' => $this->PermisoRepository->GetPermisosTipo('permiso'),
            'seccion' => $this->PermisoRepository->GetPermisosTipo('seccion'),
        ];

        $breadcrumb = [
            ['name' => 'Inicio', 'url' => route('home')],
            ['name' => 'Roles', 'url' => route('roles.index')],
        ];
        return view('roles.edit', compact('role', 'permisosPorTipo', 'breadcrumb'));
    }

    function get_permisos_menu($id, $rol_id = -1)
    {
        $permisosPorTipo = $this->PermisoRepository->GetPermisoMenu($id, $rol_id);

        return response()->json(['status' => 'success', 'permisosPorTipo' => $permisosPorTipo]);

    }


    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:roles,name,' . $id,
            'permissions' => 'array',
        ]);

        $role = Role::findOrFail($id);
        $role->name = $request->name;
        $role->save();

        // Asignar o eliminar permisos según lo que se haya seleccionado
        $role->syncPermissions($request->permissions);

        return redirect()->back()->with('success', 'Rol actualizado correctamente.');
    }


    public function destroy($id)
    {
        $role = Role::findOrFail($id);
        $role->delete();

        return redirect()->route('roles.index')->with('success', 'Rol eliminado correctamente.');
    }
}
