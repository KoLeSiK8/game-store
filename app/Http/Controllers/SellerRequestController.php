<?php

namespace App\Http\Controllers;

use App\Models\SellerRequest;
use App\Services\RoleService;
use Illuminate\Http\Request;

class SellerRequestController extends Controller
{
    /**
     * Отправка заявки на статус продавца.
     */
    public function submitRequest(Request $request)
    {
        $user = $request->user();

        if (!$user) {
            abort(401, 'Unauthorized');
        }

        $validated = $request->validate([
            'message' => ['nullable', 'string', 'max:2000'],
        ]);

        // Создаем новую заявку.
        SellerRequest::create([
            'user_id' => $user->id,
            'status' => 'pending',
            'message' => $validated['message'] ?? null,
        ]);

        return back()->with('status', 'Заявка отправлена.');
    }

    /**
     * Одобрение заявки администратором.
     */
    public function approveRequest(Request $request, SellerRequest $sellerRequest, RoleService $roleService)
    {
        $admin = $request->user();

        if (!$admin) {
            abort(401, 'Unauthorized');
        }

        $sellerRequest->update([
            'status' => 'approved',
            'reviewed_by' => $admin->id,
            'reviewed_at' => now(),
        ]);

        // Назначаем пользователю роль продавца.
        $roleService->assignRole($sellerRequest->user, 'seller');

        return back()->with('status', 'Заявка одобрена.');
    }

    /**
     * Отклонение заявки администратором.
     */
    public function rejectRequest(Request $request, SellerRequest $sellerRequest)
    {
        $admin = $request->user();

        if (!$admin) {
            abort(401, 'Unauthorized');
        }

        $sellerRequest->update([
            'status' => 'rejected',
            'reviewed_by' => $admin->id,
            'reviewed_at' => now(),
        ]);

        return back()->with('status', 'Заявка отклонена.');
    }
}