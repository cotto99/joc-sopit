<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TicketCargo extends Model
{
    protected $table = 'ticket_cargos';
    protected $fillable = [
        'ticket_id', 'descripcion',
        'cantidad', 'precio_unitario', 'subtotal'
    ];

    public function ticket()
    {
        return $this->belongsTo(Ticket::class);
    }
}