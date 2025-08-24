<?php

namespace App\Http\Controllers;

use App\Interfaces\UserInterface;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Exports\ExportExcel;
use App\Exports\ExportPDF;

use Maatwebsite\Excel\Facades\Excel;
class UserController extends Controller
{
    protected $userRepository;

    public function __construct(UserInterface $userInterface)
    {
        $this->userRepository = $userInterface;
    }

    public function index()
    {
        $users = User::paginate(5); // Trae todos los usuarios

        $breadcrumb = [
            ['name' => 'Inicio', 'url' => route('home')],
            ['name' => 'Usuarios', 'url' => route('users.index')],
        ];
        return view('usuarios.index', compact('users', 'breadcrumb'));
    }

    // Mostrar el formulario para crear un nuevo usuario
    public function create()
    {
        $breadcrumb = [
            ['name' => 'Inicio', 'url' => route('home')],
            ['name' => 'Usuarios', 'url' => route('users.index')],
            ['name' => 'Crear', 'url' => route('users.create')],
        ];
        return view('usuarios.create', compact('breadcrumb'));
    }
    public function Perfil()
    {
        $user = Auth::user();


        $breadcrumb = [
            ['name' => 'Inicio', 'url' => route('home')],
            ['name' => 'Perfil', 'url' => route('users.index')],
        ];

        return view('usuarios.perfil', compact('user', 'breadcrumb'));
    }

    // Almacenar un nuevo usuario
    public function store(Request $request)
    {
        session(['form_action' => 'store']);

        $user = $this->userRepository->CrearUsuario($request);

        $user->assignRole($request->input('role'));
        // Redirigir al usuario con un mensaje de éxito
        return redirect()->route('users.index')->with('success', 'Usuario registrado exitosamente!');
    }


    // Mostrar la información de un usuario
    public function show(User $user)
    {
        return view('users.show', compact('user'));
    }

    // Mostrar el formulario para editar un usuario
    public function edit($id)
    {
        $user = User::find($id);

        $breadcrumb = [
            ['name' => 'Inicio', 'url' => route('home')],
            ['name' => 'Usuarios', 'url' => route('users.index')],
            ['name' => 'Ecitar ' . $user->name, 'url' => route('users.index')],
        ];

        return view('usuarios.edit', compact('user', 'breadcrumb'));
    }

    // Actualizar un usuario
    public function update(request $request, $id, $perfil)
    {


        session(['form_action' => 'update']);
        session(['user_id' => $id]);

        $user = $this->userRepository->EditarUsuario($request, $id, $perfil);

        if ($perfil != 1) {
            if ($user->roles->isNotEmpty()) {
                $user->syncRoles([]);
            }
        }

        // Asignar el nuevo rol
        $user->assignRole($request->input('role'));
        if ($perfil == 1) {
            return redirect()->back()->with('status', 'Usuario actualizado exitosamente!');

        } else {
            return redirect()->route('users.index')->with('status', 'Usuario actualizado exitosamente!');

        }

    }
    public function updateDatosPersonales(request $request, $id)
    {
        session(['form_action' => 'update']);
        session(['user_id' => $id]);

        $this->userRepository->EditarDatosPersonales($request, $id);

        return redirect()->route('users.index')->with('success', 'Usuario actualizado exitosamente!');
    }
    // Eliminar un usuario
    public function destroy(User $user)
    {
        $user->delete();

        return redirect()->route('users.index')->with('success', 'Usuario eliminado exitosamente');
    }

    public function exportExcel()
    {
        $export = new ExportExcel('usuarios.export_usuarios', ['users' => User::all(), 'export' => 'Usuarios'], 'usuarios');
        return Excel::download($export, $export->getFileName());
    }

    public function exportPDF()
    {
        $users = User::all();

        return ExportPDF::exportPdf('usuarios.export_usuarios', ['users' => $users, 'export' => 'Usuarios'], 'usuarios', false);
    }

}
