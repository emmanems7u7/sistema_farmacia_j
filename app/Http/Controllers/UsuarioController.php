<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Sucursal;
use App\Models\Role;

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use App\Models\Categoria;
use App\Models\Laboratorio;
use App\Models\Lote;

use App\Models\Producto;


use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon;
use PDF;

class UsuarioController extends Controller
{

    public function index()
    {
        // Cargar todos los usuarios con relaciones (sin filtro por sucursal)
        $usuarios = User::with(['roles', 'sucursal'])
            ->get()
            ->map(function ($usuario) {
                $usuario->sucursal_nombre = $usuario->sucursal->nombre ?? 'N/A';
                return $usuario;
            });

        $sucursales = Sucursal::all();
        $roles = Role::all();

        return view('admin.usuarios.index', compact('usuarios', 'roles', 'sucursales'));
    }



    public function create()
    {

        $roles = Role::all(); // Cargamos todos los roles
        return view('admin.usuarios.create', compact('roles')); // Pasamos la variable roles a la vista
    }


    public function show(string $id)
    {
        $usuario = User::findOrFail($id); // Buscar el usuario por id
        return view('admin.usuarios.show', compact('usuario')); // retornar la vista para mostrar detalles del usuario
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $usuario = User::with('roles')->findOrFail($id);
        $roles = Role::all();
        return view('admin.usuarios.edit', compact('usuario', 'roles'));
    }


    public function destroy(string $id)
    {
        User::destroy($id); // Buscar el usuario por ID


        // Redirigir al índice con un mensaje de éxito
        return redirect()->route('admin.usuarios.index')
            ->with('mensaje', 'Usuario eliminado con éxito.')
            ->with('icono', 'success');
    }

    public function store(Request $request)
    {


        // Validación mejorada
        $validated = $request->validate([
            'firstname' => 'required|string|max:255',
            'lastname' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'username' => 'required|string|max:255|unique:users',
            'role' => 'required|exists:roles,name',
            'sucursal_id' => 'required|exists:sucursals,id',
            'address' => 'nullable|string|max:255',
            'celular' => 'nullable|string|max:20|regex:/^[0-9]+$/',
            'imagen' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ], [
            'email.unique' => 'El correo ya está registrado',
            'username.unique' => 'El nombre de usuario ya existe',
            'sucursal_id.required' => 'Seleccione una sucursal',
            'celular.regex' => 'Solo números permitidos'
        ]);

        DB::beginTransaction();

        try {
            // Procesar imagen
            $imagenPath = null;
            if ($request->hasFile('imagen')) {
                $imagenPath = $request->file('imagen')->store(
                    'usuarios',
                    'public'
                );
            }

            // Crear usuario con contraseña igual al username
            $usuario = User::create([
                'firstname' => $validated['firstname'],
                'lastname' => $validated['lastname'],
                'email' => $validated['email'],
                'username' => $validated['username'],
                'password' => Hash::make($validated['username']), // Contraseña = usernamenoolvidar
                'address' => $validated['address'],
                'celular' => $validated['celular'],
                'imagen' => $imagenPath,
                'sucursal_id' => $validated['sucursal_id'],
                'email_verified_at' => now()
            ]);

            // Asignar rol
            $usuario->assignRole($validated['role']);

            DB::commit();

            // Respuesta para AJAX
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Usuario creado exitosamente',
                    'redirect' => route('admin.usuarios.index')
                ]);
            }

            return redirect()->route('admin.usuarios.index')
                ->with('success', 'Usuario creado exitosamente');

        } catch (\Exception $e) {
            DB::rollBack();

            // Log detallado
            Log::error('Error al crear usuario', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'input' => $request->except('password')
            ]);

            // Respuesta para AJAX
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error al crear usuario: ' . $e->getMessage()
                ], 500);
            }

            return back()->withInput()
                ->with('error', 'Error al crear usuario: ' . $e->getMessage());
        }
    }

    public function update(Request $request, string $id)
    {

        // Validación de datos
        $validated = $request->validate([
            'firstname' => 'required|string|max:255',
            'lastname' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $id,
            'role' => 'required|exists:roles,name',
            'password' => 'nullable|min:8|confirmed',
            'imagen' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'username' => 'required|string|max:255|unique:users,username,' . $id,
            'address' => 'nullable|string|max:255',
            'celular' => 'nullable|string|max:20',
            'remove_image' => 'nullable|boolean'
        ]);

        try {
            $usuario = User::findOrFail($id);

            // Manejo de imagen
            if ($request->has('remove_image') && $request->remove_image) {
                // Eliminar imagen actual si existe
                if ($usuario->imagen) {
                    $oldImage = str_replace('storage/', 'public/', $usuario->imagen);
                    Storage::delete($oldImage);
                }
                $validated['imagen'] = null;
            } elseif ($request->hasFile('imagen')) {
                // Eliminar imagen anterior si existe
                if ($usuario->imagen) {
                    $oldImage = str_replace('storage/', 'public/', $usuario->imagen);
                    Storage::delete($oldImage);
                }

                // Guardar nueva imagen
                $path = $request->file('imagen')->store('public/usuarios');
                $validated['imagen'] = str_replace('public/', 'storage/', $path);
            } else {
                // Mantener la imagen existente si no se sube una nueva
                unset($validated['imagen']);
            }

            // Actualizar datos básicos
            $usuario->update($validated);

            // Actualizar contraseña si se proporcionó
            if ($request->filled('password')) {
                $usuario->update([
                    'password' => Hash::make($request->password)
                ]);
            }

            // Sincronizar roles
            $usuario->syncRoles($request->role);

            return redirect()->route('admin.usuarios.index')
                ->with('success', 'Usuario actualizado correctamente');

        } catch (\Exception $e) {
            \Log::error('Error al actualizar usuario: ' . $e->getMessage());

            return back()->withInput()
                ->with('error', 'Error al actualizar el usuario: ' . $e->getMessage());
        }
    }

    public function generarReporte($tipo)
    {
        $usuarios = User::with('sucursal')->get();

        return $this->generarReportePorTipo($tipo, $usuarios);
    }

    protected function generarReportePorTipo($tipo, $usuarios)
    {
        switch ($tipo) {
            case 'pdf':
                return $this->generarPDF($usuarios);
            case 'excel':
                return $this->generarExcel($usuarios);
            case 'csv':
                return $this->generarCSV($usuarios);
            case 'print':
                return view('admin.usuarios.reporte', [
                    'usuarios' => $usuarios,
                    'fecha_generacion' => now()->format('d/m/Y H:i:s')
                ]);
            default:
                abort(404);
        }
    }

    private function generarPDF($usuarios)
    {
        $pdf = PDF::loadView('admin.usuarios.reporte', [
            'usuarios' => $usuarios,
            'fecha_generacion' => now()->format('d/m/Y H:i:s'),
            'page' => 1,
            'pages' => 1
        ]);

        return $pdf->download('reporte_usuarios_' . now()->format('YmdHis') . '.pdf');
    }

    private function generarExcel($usuarios)
    {
        $data = $usuarios->map(function ($usuario) {
            return [
                'Nombres' => $usuario->firstname,
                'Apellidos' => $usuario->lastname,
                'Email' => $usuario->email,
                'Usuario' => $usuario->username,
                'Rol' => $usuario->role,
                'Sucursal' => $usuario->sucursal->nombre ?? 'N/A',
                'Dirección' => $usuario->address,
                'Teléfono' => $usuario->celular,

                'Registro' => $usuario->created_at->format('d/m/Y')
            ];
        });

        return Excel::download(
            new class ($data) implements FromCollection {
            private $data;
            public function __construct($data)
            {
                $this->data = $data;
            }
            public function collection()
            {
                return $this->data;
            }
            },
            'reporte_usuarios_' . now()->format('YmdHis') . '.xlsx'
        );
    }

    private function generarCSV($usuarios): BinaryFileResponse
    {
        $data = $usuarios->map(function ($usuario) {
            return [
                'Nombres' => $usuario->firstname,
                'Apellidos' => $usuario->lastname,
                'Email' => $usuario->email,
                'Usuario' => $usuario->username,
                'Rol' => $usuario->role,
                'Sucursal' => $usuario->sucursal->nombre ?? 'N/A',
                'Dirección' => $usuario->address,
                'Teléfono' => $usuario->celular,
                'Estado' => $usuario->activo ? 'Activo' : 'Inactivo',
                'Registro' => $usuario->created_at->format('d/m/Y')
            ];
        });

        return Excel::download(
            new class ($data) implements FromCollection {
            private $data;
            public function __construct($data)
            {
                $this->data = $data;
            }
            public function collection()
            {
                return $this->data;
            }
            },
            'reporte_usuarios_' . now()->format('YmdHis') . '.csv',
            \Maatwebsite\Excel\Excel::CSV
        );
    }

}