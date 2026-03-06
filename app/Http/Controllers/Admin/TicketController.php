<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use App\Models\TicketSeguimiento;
use App\Models\TicketCargo;
use App\Models\Factura;
use App\Models\Tecnico;
use App\Models\CategoriaSoporte;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;


class TicketController extends Controller
{
    public function index()
    {
        return Inertia::render('Admin/Tickets/Index', [
            'tickets'   => Ticket::with('empresa', 'sucursal', 'tecnico', 'categoria')
                ->orderByDesc('created_at')->paginate(20),
            'tecnicos'  => Tecnico::where('activo', true)->get(),
        ]);
    }

    public function show(Ticket $ticket)
    {
        $ticket->load([
            'empresa', 'sucursal', 'contacto',
            'categoria', 'tecnico',
            'seguimientos.user',
            'cargos',
            'factura',
        ]);
        return Inertia::render('Admin/Tickets/Show', [
            'ticket'     => $ticket,
            'tecnicos'   => Tecnico::where('activo', true)->get(),
            'categorias' => CategoriaSoporte::where('activo', true)->get(),
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'empresa_id'  => 'required|exists:empresas,id',
            'sucursal_id' => 'nullable|exists:sucursales,id',
            'titulo'      => 'required|string|max:255',
            'descripcion' => 'required|string',
            'categoria_id'=> 'nullable|exists:categorias_soporte,id',
            'prioridad'   => 'required|in:baja,media,alta,critica',
        ]);

        DB::transaction(function () use ($request) {
            $ticket = Ticket::create([
                'codigo'       => Ticket::generarCodigo(),
                'empresa_id'   => $request->empresa_id,
                'sucursal_id'  => $request->sucursal_id,
                'contacto_id'  => $request->contacto_id,
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
                'contenido'       => 'Ticket creado',
                'estado_anterior' => null,
                'estado_nuevo'    => 'abierto',
                'visible_cliente' => true,
            ]);
        });

        return redirect()->route('admin.tickets.index')
            ->with('success', 'Ticket creado exitosamente.');
    }

    // ✅ ESTE ES EL MÉTODO QUE TE FALTABA
    public function update(Request $request, Ticket $ticket)
    {
        $request->validate([
            'titulo'       => 'sometimes|string|max:255',
            'descripcion'  => 'sometimes|string',
            'categoria_id' => 'nullable|exists:categorias_soporte,id',
            'prioridad'    => 'sometimes|in:baja,media,alta,critica',
        ]);

        $ticket->update($request->only([
            'titulo',
            'descripcion',
            'categoria_id',
            'prioridad'
        ]));

        return redirect()->back()->with('success', 'Ticket actualizado correctamente.');
    }

    public function asignar(Request $request, Ticket $ticket)
    {
        $request->validate([
            'tecnico_id' => 'required|exists:tecnicos,id',
        ]);

        $estadoAnterior = $ticket->estado;

        $ticket->update([
            'tecnico_id'  => $request->tecnico_id,
            'estado'      => 'asignado',
            'fecha_inicio'=> now(),
        ]);

        TicketSeguimiento::create([
            'ticket_id'       => $ticket->id,
            'user_id'         => auth()->id(),
            'tipo'            => 'estado',
            'contenido'       => 'Ticket asignado a técnico',
            'estado_anterior' => $estadoAnterior,
            'estado_nuevo'    => 'asignado',
            'visible_cliente' => true,
        ]);

        return redirect()->back()->with('success', 'Ticket asignado.');
    }

    public function cambiarEstado(Request $request, Ticket $ticket)
    {
        $request->validate([
            'estado'          => 'required|in:abierto,asignado,en_proceso,en_espera,resuelto,cerrado',
            'comentario'      => 'nullable|string',
            'visible_cliente' => 'boolean',
            'foto_evidencia'  => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
        ]);

        $fotoUrl = null;
        if ($request->hasFile('foto_evidencia')) {
            $path = $request->file('foto_evidencia')
                ->store('evidencias/' . $ticket->id, 's3');
                $fotoUrl = Storage::disk('s3')->temporaryUrl($path, now()->addHour());        }

        $estadoAnterior = $ticket->estado;
        $datos = ['estado' => $request->estado];
        if ($request->estado === 'resuelto') {
            $datos['fecha_resolucion'] = now();
        }
        $ticket->update($datos);

        TicketSeguimiento::create([
            'ticket_id'       => $ticket->id,
            'user_id'         => auth()->id(),
            'tipo'            => 'estado',
            'contenido'       => $request->comentario ?: 'Estado actualizado',
            'estado_anterior' => $estadoAnterior,
            'estado_nuevo'    => $request->estado,
            'visible_cliente' => $request->visible_cliente ?? true,
            'foto_evidencia'  => $fotoUrl,
        ]);

        return redirect()->back()->with('success', 'Estado actualizado.');
    }

    public function agregarComentario(Request $request, Ticket $ticket)
    {
        $request->validate([
            'contenido'       => 'required|string',
            'visible_cliente' => 'boolean',
            'foto_evidencia'  => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
        ]);

        $fotoUrl = null;
        if ($request->hasFile('foto_evidencia')) {
            $path = $request->file('foto_evidencia')
                ->store('evidencias/' . $ticket->id, 's3');
                $fotoUrl = Storage::disk('s3')->temporaryUrl($path, now()->addHour());        }

        TicketSeguimiento::create([
            'ticket_id'       => $ticket->id,
            'user_id'         => auth()->id(),
            'tipo'            => 'comentario',
            'contenido'       => $request->contenido,
            'visible_cliente' => $request->visible_cliente ?? true,
            'foto_evidencia'  => $fotoUrl,
        ]);

        return redirect()->back()->with('success', 'Comentario agregado.');
    }


    public function agregarCargo(Request $request, Ticket $ticket)
    {
        $request->validate([
            'descripcion'    => 'required|string',
            'cantidad'       => 'required|numeric|min:0.01',
            'precio_unitario'=> 'required|numeric|min:0',
        ]);

        $subtotal = $request->cantidad * $request->precio_unitario;

        TicketCargo::create([
            'ticket_id'      => $ticket->id,
            'descripcion'    => $request->descripcion,
            'cantidad'       => $request->cantidad,
            'precio_unitario'=> $request->precio_unitario,
            'subtotal'       => $subtotal,
        ]);

        TicketSeguimiento::create([
            'ticket_id'       => $ticket->id,
            'user_id'         => auth()->id(),
            'tipo'            => 'accion',
            'contenido'       => "Cargo agregado: {$request->descripcion} — Q{$subtotal}",
            'visible_cliente' => false,
        ]);

        return redirect()->back()->with('success', 'Cargo agregado.');
    }

    public function eliminarCargo(TicketCargo $cargo)
    {
        $cargo->delete();
        return redirect()->back()->with('success', 'Cargo eliminado.');
    }

    public function generarFactura(Request $request, Ticket $ticket)
    {
        if ($ticket->factura) {
            return redirect()->back()->withErrors(['error' => 'Ya tiene factura generada.']);
        }

        $request->validate([
            'aplica_iva'     => 'boolean',
            'porcentaje_iva' => 'nullable|numeric|min:0|max:100',
            'notas'          => 'nullable|string',
        ]);

        $subtotal    = $ticket->total_cargos;
        $aplicaIva   = $request->boolean('aplica_iva', false);
        $pctIva      = $aplicaIva ? (float)($request->porcentaje_iva ?? 12) : 0;
        $impuesto    = $aplicaIva ? round($subtotal * ($pctIva / 100), 2) : 0;
        $total       = $subtotal + $impuesto;

        Factura::create([
            'numero'         => Factura::generarNumero(),
            'ticket_id'      => $ticket->id,
            'empresa_id'     => $ticket->empresa_id,
            'subtotal'       => $subtotal,
            'aplica_iva'     => $aplicaIva,
            'porcentaje_iva' => $pctIva,
            'impuesto'       => $impuesto,
            'total'          => $total,
            'estado'         => 'pendiente',
            'notas'          => $request->notas,
        ]);

        return redirect()->back()->with('success', 'Factura generada.');
    }
}