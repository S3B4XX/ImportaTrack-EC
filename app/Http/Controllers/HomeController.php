<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HomeController extends Controller
{
    /** Envía a cada usuario al área de su rol. */
    public function __invoke(Request $request)
    {
        if ($request->user()->isAdmin()) {
            return redirect()->route('admin.dashboard');
        }

        // Las pantallas de Operador y Cliente forman parte del Core (Progreso 2 / Tarea 10).
        return view('home', ['user' => $request->user()->load('role')]);
    }
}
