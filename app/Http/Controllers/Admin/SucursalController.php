<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Sucursal;
use App\Models\Empresa;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SucursalController extends Controller
{
    public function index()
    {
        return Inertia::render('Admin/Sucursales/Index', [
            'sucursales' => Sucursal::with('empresa')
                ->withCount('tickets')
                ->orderBy('nombre')->get(),
            'empresas' => Empresa::where('activo', true)
                ->orderBy('nombre')->get(),
        ]);
    }

    public function store(Request $request, Empresa $empresa = null)
    {
        $request->validate([
            'empresa_id' => 'required|exists:empresas,id',
            'nombre'     => 'required|string|max:255',
            'direccion'  => 'nullable|string',
            'telefono'   => 'nullable|string|max:20',
        ]);

        Sucursal::create([
            'empresa_id' => $request->empresa_id,
            'nombre'     => $request->nombre,
            'direccion'  => $request->direccion,
            'telefono'   => $request->telefono,
        ]);

        return redirect()->back()->with('success', 'Sucursal creada.');
    }

    public function update(Request $request, Sucursal $sucursal)
    {
        $request->validate([
            'nombre'    => 'required|string|max:255',
            'direccion' => 'nullable|string',
            'telefono'  => 'nullable|string|max:20',
        ]);

        $sucursal->update($request->only('nombre', 'direccion', 'telefono'));
        return redirect()->back()->with('success', 'Sucursal actualizada.');
    }

    public function destroy(Sucursal $sucursal)
    {
        $sucursal->update(['activo' => false]);
        return redirect()->back()->with('success', 'Sucursal desactivada.');
    }
}