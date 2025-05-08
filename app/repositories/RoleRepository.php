<?php
namespace App\Repositories;

use App\Interfaces\RoleInterface;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
class RoleRepository extends BaseRepository implements RoleInterface
{

    public function __construct()
    {
        parent::__construct();

    }
    public function CrearRol($request)
    {

        $role = Role::create([
            'name' => $this->cleanHtml($request->name),
        ]);


        if ($request->has(key: 'permissions')) {
            $role->givePermissionTo($request->permissions);
        }
    }

}
