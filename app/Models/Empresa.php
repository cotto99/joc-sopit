<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Empresa extends Model
{
    protected $table = 'empresas';
    protected $fillable = [
        'nombre', 'nit', 'telefono', 'email', 'direccion', 'logo', 'activo'
    ];

    public function sucursales()
    {
        return $this->hasMany(Sucursal::class);
    }

    public function contactos()
    {
        return $this->hasMany(Contacto::class);
    }

    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }

    public function usuarios()
    {
        return $this->hasMany(User::class);
    }
}