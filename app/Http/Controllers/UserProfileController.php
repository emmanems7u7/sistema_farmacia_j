<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class UserProfileController extends Controller
{
    public function show()
    {
        return view('pages.user-profile');
    }

    public function update(Request $request)
    {
        $attributes = $request->validate([
            'username' => ['required','max:255', 'min:2'],
            'firstname' => ['max:100'],
            'lastname' => ['max:100'],
            'email' => ['required', 'email', 'max:255',  Rule::unique('users')->ignore(auth()->user()->id),],
            'address' => ['max:100'],
            
            'celular' => ['nullable', 'string', 'max:20'],
            'imagen' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'] 
        ]);

        auth()->user()->update([
            'username' => $request->get('username'),
            'firstname' => $request->get('firstname'),
            'lastname' => $request->get('lastname'),
            'email' => $request->get('email') ,
            'address' => $request->get('address'),
            
            'celular' => $request->get('celular'),
            'imagen' => $request->hasFile('imagen')
        ? $request->file('imagen')->store('imagen', 'public') 
        : auth()->user()->imagen
        ]);
        return back()->with('succes', 'Modificacion exitosa');
    }


   
}