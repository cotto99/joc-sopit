<?php
namespace App\Http\Controllers\Cliente;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use App\Models\TicketSeguimiento;
use App\Models\CategoriaSoporte;
use App\Models\Sucursal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class TicketController extends Controller
{
    public function index()
    {
        $empresa_id = auth()->user()->empresa_id;

        return Inertia::render('Cliente/Tickets/Index', [
            'tickets' => Ticket::with('sucursal', 'categoria', 'tecnico')
                ->where('empresa_id', $empresa_id)
                ->orderByDesc('created_at')
                ->paginate(15),
        ]);
    }

    public function show(Ticket $ticket)
    {
        // Verificar que el ticket pertenece a la empresa del cliente
        if ($ticket->empresa_id !== auth()->user()->empresa_id) {
            abort(403);
        }

        $ticket->load([
            'empresa', 'sucursal', 'categoria', 'tecnico',
            'seguimientos' => fn($q) => $q->where('visible_cliente', true),
            'seguimientos.user',
            'cargos',
            'factura',
        ]);

        return Inertia::render('Cliente/Tickets/Show', [
            'ticket' => $ticket,
        ]);
    }

    public function create()
    {
        $empresa_id = auth()->user()->empresa_id;

        return Inertia::render('Cliente/Tickets/Create', [
            'sucursales' => Sucursal::where('empresa_id', $empresa_id)
                ->where('activo', true)->get(),
            'categorias' => CategoriaSoporte::where('activo', true)->get(),
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'sucursal_id' => 'nullable|exists:sucursales,id',
            'categoria_id'=> 'nullable|exists:categorias_soporte,id',
            'titulo'      => 'required|string|max:255',
            'descripcion' => 'required|string',
            'prioridad'   => 'required|in:baja,media,alta,critica',
        ]);

        DB::transaction(function () use ($request) {
            $ticket = Ticket::create([
                'codigo'       => Ticket::generarCodigo(),
                'empresa_id'   => auth()->user()->empresa_id,
                'sucursal_id'  => $request->sucursal_id,
                'categoria_id' => $request->categoria_id,
                'titulo'       => $request->titulo,
                'descripcion'  => $request->descripcion,
                'prioridad'    => $request->prioridad,
                'estado'       => 'abierto',
                'creado_por'   => auth()->id(),
            ]);

            TicketSeguimiento::create([
                'ticket_id'       => $ticket->id,
                'user_id'         => auth()->id(),
                'tipo'            => 'estado',
                'contenido'       => 'Ticket creado por el cliente',
                'estado_nuevo'    => 'abierto',
                'visible_cliente' => true,
            ]);
        });

        return redirect()->route('cliente.tickets.index')
            ->with('success', 'Ticket creado exitosamente.');
    }

    public function agregarComentario(Request $request, Ticket $ticket)
    {
        if ($ticket->empresa_id !== auth()->user()->empresa_id) {
            abort(403);
        }

        $request->validate(['contenido' => 'required|string']);

        TicketSeguimiento::create([
            'ticket_id'       => $ticket->id,
            'user_id'         => auth()->id(),
            'tipo'            => 'comentario',
            'contenido'       => $request->contenido,
            'visible_cliente' => true,
        ]);

        return redirect()->back()->with('success', 'Comentario enviado.');
    }
}