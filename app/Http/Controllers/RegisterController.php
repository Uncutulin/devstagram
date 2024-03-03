<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;

class RegisterController extends Controller
{
    public function index() 
    {
        return view('auth.register');
    }

    public function store(Request $request) 
    {

        //Modificar el Request
        $request->request->add(['username' => Str::slug($request->username)]);        

        //Validación
        $this->validate($request, [
            'name' => 'required|max:30',
            'username' => 'required|unique:users|min:3|max:20',
            'email' => 'required|unique:users|email|max:60',
            'password' => 'required|confirmed',
        ]);
        
        //slug() reemplaza los espacios por "-"
        User::create([
            'name' => $request->name,
            'username' => $request->username,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        //Autenticar un Usuario

        auth()->attempt([
            'email' => $request->email,
            'password' => $request->password
        ]);

        //Redireccionar
        return Redirect()->route('posts.index', auth()->user());
    }
}
