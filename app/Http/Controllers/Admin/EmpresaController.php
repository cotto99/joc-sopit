<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Empresa;
use Illuminate\Http\Request;
use Inertia\Inertia;

class EmpresaController extends Controller
{
    public function index()
    {
        return Inertia::render('Admin/Empresas/Index', [
            'empresas' => Empresa::withCount(['sucursales', 'tickets'])
                ->orderBy('nombre')->get()
        ]);
    }

    public function show(Empresa $empresa)
    {
        $empresa->load([
            'sucursales',
            'contactos.sucursal',
            'contactos.user',
            'tickets' => fn($q) => $q->latest()->take(10)
        ]);
        return Inertia::render('Admin/Empresas/Show', [
            'empresa' => $empresa
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre'   => 'required|string|max:255',
            'nit'      => 'nullable|string|max:20',
            'telefono' => 'nullable|string|max:20',
            'email'    => 'nullable|email',
            'direccion'=> 'nullable|string',
        ]);
        Empresa::create($request->all());
        return redirect()->back()->with('success', 'Empresa creada.');
    }

    public function update(Request $request, Empresa $empresa)
    {
        $request->validate([
            'nombre'   => 'required|string|max:255',
            'nit'      => 'nullable|string|max:20',
            'telefono' => 'nullable|string|max:20',
            'email'    => 'nullable|email',
            'direccion'=> 'nullable|string',
        ]);
        $empresa->update($request->all());
        return redirect()->back()->with('success', 'Empresa actualizada.');
    }

    public function destroy(Empresa $empresa)
    {
        $empresa->update(['activo' => false]);
        return redirect()->back()->with('success', 'Empresa desactivada.');
    }
}