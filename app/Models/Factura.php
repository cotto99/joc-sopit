<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Factura extends Model
{
    protected $table = 'facturas';
    protected $fillable = [
        'numero', 'ticket_id', 'empresa_id',
        'subtotal', 'aplica_iva', 'porcentaje_iva',
        'impuesto', 'total', 'estado', 'notas'
    ];

    public function ticket()
    {
        return $this->belongsTo(Ticket::class);
    }

    public function empresa()
    {
        return $this->belongsTo(Empresa::class);
    }

    public static function generarNumero(): string
    {
        $ultimo = self::latest()->first();
        $numero = $ultimo ? (intval(substr($ultimo->numero, -6)) + 1) : 1;
        return 'FAC-' . date('Y') . '-' . str_pad($numero, 6, '0', STR_PAD_LEFT);
    }
}