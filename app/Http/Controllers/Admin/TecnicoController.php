<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tecnico;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class TecnicoController extends Controller
{
    public function index()
    {
        return Inertia::render('Admin/Tecnicos/Index', [
            'tecnicos' => Tecnico::with('user')
                ->withCount('tickets')
                ->orderBy('nombre')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre'       => 'required|string|max:255',
            'apellido'     => 'required|string|max:255',
            'email'        => 'required|email|unique:users,email',
            'password'     => 'required|string|min:6',
            'telefono'     => 'nullable|string|max:20',
            'especialidad' => 'nullable|string|max:255',
        ]);

        DB::transaction(function () use ($request) {
            $user = User::create([
                'name'     => $request->nombre . ' ' . $request->apellido,
                'email'    => $request->email,
                'password' => Hash::make($request->password),
                'rol'      => 'tecnico',
            ]);

            Tecnico::create([
                'user_id'      => $user->id,
                'nombre'       => $request->nombre,
                'apellido'     => $request->apellido,
                'telefono'     => $request->telefono,
                'especialidad' => $request->especialidad,
            ]);
        });

        return redirect()->back()->with('success', 'Técnico creado.');
    }

    public function update(Request $request, Tecnico $tecnico)
    {
        $request->validate([
            'nombre'       => 'required|string|max:255',
            'apellido'     => 'required|string|max:255',
            'telefono'     => 'nullable|string|max:20',
            'especialidad' => 'nullable|string|max:255',
        ]);

        $tecnico->update($request->only('nombre', 'apellido', 'telefono', 'especialidad'));

        if ($tecnico->user) {
            $tecnico->user->update([
                'name' => $request->nombre . ' ' . $request->apellido,
            ]);
        }

        return redirect()->back()->with('success', 'Técnico actualizado.');
    }

    public function destroy(Tecnico $tecnico)
    {
        $tecnico->update(['activo' => false]);
        return redirect()->back()->with('success', 'Técnico desactivado.');
    }
}