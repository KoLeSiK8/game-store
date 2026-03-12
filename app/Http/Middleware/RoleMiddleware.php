<?php

namespace App\Http\Middleware;

use App\Services\RoleService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * @var RoleService
     */
    private RoleService $roleService;

    /**
     * Создаем middleware с сервисом ролей.
     */
    public function __construct(RoleService $roleService)
    {
        $this->roleService = $roleService;
    }

    /**
     * Проверяет наличие указанной роли у пользователя.
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {
        $user = $request->user();

        // Если пользователь не авторизован.
        if (!$user) {
            abort(401, 'Unauthorized');
        }

        // Проверяем наличие роли.
        if (!$this->roleService->hasRole($user, $role)) {
            abort(403, 'Forbidden');
        }

        return $next($request);
    }
}
