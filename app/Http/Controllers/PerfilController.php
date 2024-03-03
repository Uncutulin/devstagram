<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Intervention\Image\Facades\Image;

class PerfilController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
        
    public function index()
    {
        return view('perfil.index');
    }

    public function store(Request $request)
    {
        $request->request->add(['username' => Str::slug($request->username)]);    
        $request->validate([
            'username' => ['required', 'unique:users,username,'.auth()->user()->id, 'min:3', 'max:20', 'not_in:twitter,editar-perfil']
            //'username' => ['required', 'unique:users', 'min:3', 'max:20', 'not_in:twitter,editar-perfil', 'in:CLIENTE'] in elije dentro de esa lista
        ]);

        if($request->imagen){
            $imagen = $request->file('imagen');
 
            $nombreImagen = Str::uuid() . "." . $imagen->extension();
            $imagenServidor = Image::make($imagen);
            $imagenServidor->fit(1000, 1000);
     
            $imagenPath = public_path('perfiles') . '/' . $nombreImagen;
            $imagenServidor->save($imagenPath);   
        }

        //Guardar cambios
        $usuario = User::find(auth()->user()->id);
        $usuario -> username = $request->username;
        $usuario -> imagen = $nombreImagen ?? auth()->user()->imagen ?? null;
        $usuario -> save();
        return redirect()->route('posts.index', $usuario->username);
    }
}
