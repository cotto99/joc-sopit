<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CategoriaSoporte;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CategoriaController extends Controller
{
    public function index()
    {
        return Inertia::render('Admin/Categorias/Index', [
            'categorias' => CategoriaSoporte::withCount('tickets')
                ->orderBy('nombre')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre'          => 'required|string|max:255',
            'descripcion'     => 'nullable|string',
            'tipo'            => 'required|in:remoto,presencial,instalacion,mantenimiento',
            'precio_base'     => 'required|numeric|min:0',
            'precio_variable' => 'boolean',
        ]);

        CategoriaSoporte::create($request->all());
        return redirect()->back()->with('success', 'Categoría creada.');
    }

    public function update(Request $request, CategoriaSoporte $categoria)
    {
        $request->validate([
            'nombre'          => 'required|string|max:255',
            'descripcion'     => 'nullable|string',
            'tipo'            => 'required|in:remoto,presencial,instalacion,mantenimiento',
            'precio_base'     => 'required|numeric|min:0',
            'precio_variable' => 'boolean',
        ]);

        $categoria->update($request->all());
        return redirect()->back()->with('success', 'Categoría actualizada.');
    }

    public function destroy(CategoriaSoporte $categoria)
    {
        $categoria->update(['activo' => false]);
        return redirect()->back()->with('success', 'Categoría desactivada.');
    }
}