<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use phpDocumentor\Reflection\Types\Self_;

class IsSuperAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    const ROLE_SUPER_ADMIN = 3;
    public function handle(Request $request, Closure $next)
    {
        //creo la variable userId para almacenar el id traido del token
        $userId = auth()->user()->id;
        //creo el objeto user desde el modelo user q corresponde con el id, lo necesito para acceder a su rol
        $user = User::find($userId);

        $isSuperAdmin = $user->roles->contains(self::ROLE_SUPER_ADMIN);

        if (!$isSuperAdmin) {
            return response()->json([
                'success' => false,
                'message' => 'No existe esta ruta'
            ],
            404
            );
        }
        return $next($request);
    }
}
