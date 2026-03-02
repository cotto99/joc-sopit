<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Factura;
use Illuminate\Http\Request;
use Inertia\Inertia;

class FacturaController extends Controller
{
    public function index()
    {
        return Inertia::render('Admin/Facturas/Index', [
            'facturas' => Factura::with('empresa', 'ticket')
                ->orderByDesc('created_at')
                ->paginate(20),
        ]);
    }

    public function show(Factura $factura)
    {
        $factura->load('empresa', 'ticket.cargos', 'ticket.tecnico');
        return Inertia::render('Admin/Facturas/Show', [
            'factura' => $factura,
        ]);
    }

    public function actualizarEstado(Request $request, Factura $factura)
    {
        $request->validate([
            'estado' => 'required|in:pendiente,pagada,anulada',
        ]);
        $factura->update(['estado' => $request->estado]);
        return redirect()->back()->with('success', 'Estado actualizado.');
    }
}