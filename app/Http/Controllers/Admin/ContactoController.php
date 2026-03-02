<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Contacto;
use App\Models\Empresa;
use App\Models\Sucursal;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;

class ContactoController extends Controller
{
    public function index()
    {
        return Inertia::render('Admin/Contactos/Index', [
            'contactos' => Contacto::with('empresa', 'sucursal', 'user')
                ->orderBy('nombre')->get(),
            'empresas'  => Empresa::where('activo', true)
                ->with('sucursales')->orderBy('nombre')->get(),
        ]);
    }

    public function store(Request $request, Empresa $empresa = null)
    {
        $request->validate([
            'empresa_id'  => 'required|exists:empresas,id',
            'nombre'      => 'required|string|max:255',
            'apellido'    => 'required|string|max:255',
            'email'       => 'required|email|unique:users,email',
            'password'    => 'required|string|min:6',
            'telefono'    => 'nullable|string',
            'cargo'       => 'nullable|string',
            'sucursal_id' => 'nullable|exists:sucursales,id',
        ]);

        DB::transaction(function () use ($request) {
            $user = User::create([
                'name'       => $request->nombre . ' ' . $request->apellido,
                'email'      => $request->email,
                'password'   => Hash::make($request->password),
                'rol'        => 'cliente',
                'empresa_id' => $request->empresa_id,
            ]);

            Contacto::create([
                'empresa_id'  => $request->empresa_id,
                'sucursal_id' => $request->sucursal_id,
                'user_id'     => $user->id,
                'nombre'      => $request->nombre,
                'apellido'    => $request->apellido,
                'telefono'    => $request->telefono,
                'cargo'       => $request->cargo,
            ]);
        });

        return redirect()->back()->with('success', 'Contacto creado con acceso al sistema.');
    }

    public function update(Request $request, Contacto $contacto)
    {
        $request->validate([
            'nombre'      => 'required|string|max:255',
            'apellido'    => 'required|string|max:255',
            'telefono'    => 'nullable|string',
            'cargo'       => 'nullable|string',
            'sucursal_id' => 'nullable|exists:sucursales,id',
        ]);

        $contacto->update($request->only(
            'nombre', 'apellido', 'telefono', 'cargo', 'sucursal_id'
        ));

        if ($contacto->user) {
            $contacto->user->update([
                'name' => $request->nombre . ' ' . $request->apellido,
            ]);
        }

        return redirect()->back()->with('success', 'Contacto actualizado.');
    }

    public function destroy(Contacto $contacto)
    {
        $contacto->update(['activo' => false]);
        if ($contacto->user) {
            $contacto->user->update(['activo' => false]);
        }
        return redirect()->back()->with('success', 'Contacto desactivado.');
    }
}