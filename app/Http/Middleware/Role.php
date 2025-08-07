<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Enums\Role as EnumRole; 
use Symfony\Component\HttpFoundation\Response as ResponseCode;

class Role
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     * @param mixed ...$roles
     * @return Response|RedirectResponse
     */
  public function handle(Request $request, Closure $next, ...$roles)
{
    $user = auth()->user();

    // cek apakah enum, lalu ambil value-nya
    $userRole = is_object($user->role) && method_exists($user->role, 'value')
        ? $user->role->value
        : (string) $user->role;

    if (!in_array($userRole, $roles, true)) {
        \Log::warning('FORBIDDEN ACCESS', [
    'user_id' => $user->id,
    'user_role' => $userRole,
    'allowed_roles' => $roles,
]);

        abort(ResponseCode::HTTP_FORBIDDEN);
    }

    return $next($request);
}


}
