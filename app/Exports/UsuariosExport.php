<?php

namespace App\Exports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\FromCollection;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class UsuariosExport implements FromView
{
    public function view(): View
    {
        return view('usuarios.tabla_usuarios', [
            'users' => User::all(),
            'export' => 1
        ]);
    }
}
