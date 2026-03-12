<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use App\Services\RoleService;
use Illuminate\Http\Request;

class AdminUserController extends Controller
{
    /**
     * Список пользователей.
     */
    public function listUsers()
    {
        $users = User::with('roles')
            ->orderBy('id')
            ->paginate(20);

        $roles = Role::orderBy('name')->get();

        return view('admin.users.index', [
            'users' => $users,
            'roles' => $roles,
        ]);
    }

    /**
     * Заблокировать пользователя.
     */
    public function banUser(Request $request, User $user)
    {
        $admin = $request->user();

        if (!$admin) {
            abort(401, 'Unauthorized');
        }

        // Переключаем статус блокировки.
        $user->is_banned = !$user->is_banned;
        $user->save();

        return back()->with(
            'status',
            $user->is_banned ? 'Пользователь заблокирован.' : 'Пользователь разблокирован.'
        );
    }

    /**
     * Назначить роль пользователю.
     */
    public function assignRole(Request $request, User $user, RoleService $roleService)
    {
        $admin = $request->user();

        if (!$admin) {
            abort(401, 'Unauthorized');
        }

        $validated = $request->validate([
            'role' => ['required', 'string', 'exists:roles,name'],
        ]);

        // Заменяем все роли на выбранную.
        $role = Role::where('name', $validated['role'])->first();
        if (!$role) {
            abort(404, 'Role not found');
        }

        $user->roles()->sync([$role->id]);

        return back()->with('status', 'Роль назначена.');
    }
}
