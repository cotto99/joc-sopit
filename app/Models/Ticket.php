<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    protected $table = 'tickets';
    protected $fillable = [
        'codigo', 'empresa_id', 'sucursal_id', 'contacto_id',
        'categoria_id', 'tecnico_id', 'creado_por',
        'titulo', 'descripcion', 'estado', 'prioridad',
        'fecha_inicio', 'fecha_resolucion', 'observaciones_internas'
    ];

    protected $casts = [
        'fecha_inicio'      => 'datetime',
        'fecha_resolucion'  => 'datetime',
    ];

    public function empresa()
    {
        return $this->belongsTo(Empresa::class);
    }

    public function sucursal()
    {
        return $this->belongsTo(Sucursal::class);
    }

    public function contacto()
    {
        return $this->belongsTo(Contacto::class);
    }

    public function categoria()
    {
        return $this->belongsTo(CategoriaSoporte::class, 'categoria_id');
    }

    public function tecnico()
    {
        return $this->belongsTo(Tecnico::class);
    }

    public function creadoPor()
    {
        return $this->belongsTo(User::class, 'creado_por');
    }

    public function seguimientos()
    {
        return $this->hasMany(TicketSeguimiento::class)->orderBy('created_at');
    }

    public function cargos()
    {
        return $this->hasMany(TicketCargo::class);
    }

    public function factura()
    {
        return $this->hasOne(Factura::class);
    }

    public function getTotalCargosAttribute()
    {
        return $this->cargos->sum('subtotal');
    }

    public static function generarCodigo(): string
    {
        $ultimo = self::latest()->first();
        $numero = $ultimo ? (intval(substr($ultimo->codigo, -5)) + 1) : 1;
        return 'TKT-' . date('Y') . '-' . str_pad($numero, 5, '0', STR_PAD_LEFT);
    }
}