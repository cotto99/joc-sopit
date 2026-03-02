<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckRol
{
    public function handle(Request $request, Closure $next, string ...$roles): mixed
    {
        if (!$request->user() || !in_array($request->user()->rol, $roles)) {
            abort(403, 'No tenés permiso para acceder a esta sección.');
        }
        return $next($request);
    }
}