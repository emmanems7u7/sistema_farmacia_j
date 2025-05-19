<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Interfaces\UserInterface;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserApiController extends Controller
{
    protected $userRepository;

    public function __construct(UserInterface $userInterface)
    {
        $this->userRepository = $userInterface;
    }

    // GET /api/users
    public function index()
    {
        $users = User::paginate(10);
        return response()->json($users);
    }

    // GET /api/users/{id}
    public function show($id)
    {
        $user = User::findOrFail($id);
        return response()->json($user);
    }

    // POST /api/users
    public function store(Request $request)
    {

        $user = $this->userRepository->CrearUsuario($request);
        $user->assignRole($request->input('role'));

        return response()->json(['message' => 'Usuario creado exitosamente', 'user' => $user], 201);
    }

    // PUT /api/users/{id}
    public function update(Request $request, $id)
    {


        $user = $this->userRepository->EditarUsuario($request, $id, 0);

        if ($user->roles->isNotEmpty()) {
            $user->syncRoles([]);
        }

        $user->assignRole($request->input('role'));

        return response()->json(['message' => 'Usuario actualizado exitosamente', 'user' => $user]);
    }

    // DELETE /api/users/{id}
    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return response()->json(['message' => 'Usuario eliminado correctamente']);
    }

    // GET /api/profile
    public function profile()
    {
        $user = Auth::user();
        return response()->json($user);
    }
}
