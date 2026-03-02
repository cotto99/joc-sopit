<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name', 'email', 'password', 'rol', 'empresa_id'
    ];

    protected $hidden = ['password', 'remember_token'];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password'          => 'hashed',
    ];

    public function empresa()
    {
        return $this->belongsTo(Empresa::class);
    }

    public function tecnico()
    {
        return $this->hasOne(Tecnico::class);
    }

    public function contacto()
    {
        return $this->hasOne(Contacto::class);
    }

    public function esAdmin(): bool
    {
        return $this->rol === 'admin';
    }

    public function esTecnico(): bool
    {
        return $this->rol === 'tecnico';
    }

    public function esCliente(): bool
    {
        return $this->rol === 'cliente';
    }
}