<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sucursal extends Model
{
    protected $table = 'sucursales';
    protected $fillable = [
        'empresa_id', 'nombre', 'direccion', 'telefono', 'activo'
    ];

    public function empresa()
    {
        return $this->belongsTo(Empresa::class);
    }

    public function contactos()
    {
        return $this->hasMany(Contacto::class);
    }

    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }
}