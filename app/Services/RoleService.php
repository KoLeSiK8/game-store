<?php

namespace App\Services;

use App\Models\Role;
use App\Models\User;
use InvalidArgumentException;

class RoleService
{
    /**
     * Назначить роль пользователю.
     */
    public function assignRole(User $user, Role|string $role): void
    {
        $roleModel = $this->resolveRole($role);

        // Без дублей привязываем роль к пользователю.
        $user->roles()->syncWithoutDetaching([$roleModel->id]);
    }

    /**
     * Проверить, есть ли у пользователя роль.
     */
    public function hasRole(User $user, Role|string $role): bool
    {
        $roleName = $role instanceof Role ? $role->name : $role;

        return $user->roles()
            ->where('name', $roleName)
            ->exists();
    }

    /**
     * Проверить, является ли пользователь администратором.
     */
    public function isAdmin(User $user): bool
    {
        return $this->hasRole($user, 'admin');
    }

    /**
     * Проверить, является ли пользователь продавцом.
     */
    public function isSeller(User $user): bool
    {
        return $this->hasRole($user, 'seller');
    }

    /**
     * Привести роль к модели Role.
     */
    private function resolveRole(Role|string $role): Role
    {
        if ($role instanceof Role) {
            return $role;
        }

        $roleModel = Role::where('name', $role)->first();

        if (!$roleModel) {
            throw new InvalidArgumentException("Role not found: {$role}");
        }

        return $roleModel;
    }
}
