<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TicketSeguimiento extends Model
{
    protected $table = 'ticket_seguimientos';
    protected $fillable = [
        'ticket_id', 'user_id', 'tipo',
        'contenido', 'estado_anterior',
        'estado_nuevo', 'visible_cliente'
    ];

    public function ticket()
    {
        return $this->belongsTo(Ticket::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}