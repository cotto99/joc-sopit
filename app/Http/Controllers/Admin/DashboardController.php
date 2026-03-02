<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use App\Models\Empresa;
use App\Models\Tecnico;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index()
    {
        return Inertia::render('Admin/Dashboard', [
            'stats' => [
                'abiertos'   => Ticket::where('estado', 'abierto')->count(),
                'asignados'  => Ticket::where('estado', 'asignado')->count(),
                'en_proceso' => Ticket::where('estado', 'en_proceso')->count(),
                'en_espera'  => Ticket::where('estado', 'en_espera')->count(),
                'resueltos'  => Ticket::where('estado', 'resuelto')->count(),
                'cerrados'   => Ticket::where('estado', 'cerrado')->count(),
                'empresas'   => Empresa::where('activo', true)->count(),
                'tecnicos'   => Tecnico::where('activo', true)->count(),
            ],
            'tickets_recientes' => Ticket::with('empresa', 'sucursal', 'tecnico', 'categoria')
                ->orderByDesc('created_at')
                ->take(10)
                ->get(),
            'tickets_criticos' => Ticket::with('empresa', 'sucursal')
                ->where('prioridad', 'critica')
                ->whereNotIn('estado', ['resuelto', 'cerrado'])
                ->orderByDesc('created_at')
                ->take(5)
                ->get(),
        ]);
    }
}