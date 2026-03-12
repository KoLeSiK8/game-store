<?php

namespace App\Http\Middleware;

use App\Services\RoleService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
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
     * Проверяет, что пользователь администратор.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        // Если пользователь не авторизован.
        if (!$user) {
            abort(401, 'Unauthorized');
        }

        // Проверяем роль администратора.
        if (!$this->roleService->isAdmin($user)) {
            abort(403, 'Forbidden');
        }

        return $next($request);
    }
}
