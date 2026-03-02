<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CategoriaSoporte extends Model
{
    protected $table = 'categorias_soporte';
    protected $fillable = [
        'nombre', 'descripcion', 'tipo',
        'precio_base', 'precio_variable', 'activo'
    ];

    public function tickets()
    {
        return $this->hasMany(Ticket::class, 'categoria_id');
    }
}