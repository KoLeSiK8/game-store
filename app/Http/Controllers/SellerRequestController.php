<?php

namespace App\Http\Controllers;

use App\Models\SellerRequest;
use App\Services\RoleService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SellerRequestController extends Controller
{
    /**
     * Форма заявки на статус продавца.
     */
    public function create(Request $request, RoleService $roleService): View|RedirectResponse
    {
        $user = $request->user();

        if (!$user) {
            abort(401, 'Unauthorized');
        }

        if ($roleService->isSeller($user)) {
            return redirect()->route('seller.dashboard');
        }

        $latestRequest = SellerRequest::where('user_id', $user->id)
            ->latest('created_at')
            ->first();

        return view('seller.requests.create', [
            'latestRequest' => $latestRequest,
        ]);
    }

    /**
     * Отправка заявки на статус продавца.
     */
    public function submitRequest(Request $request, RoleService $roleService): RedirectResponse
    {
        $user = $request->user();

        if (!$user) {
            abort(401, 'Unauthorized');
        }

        if ($roleService->isSeller($user)) {
            return redirect()->route('seller.dashboard');
        }

        $existingPending = SellerRequest::where('user_id', $user->id)
            ->where('status', 'pending')
            ->exists();

        if ($existingPending) {
            return back()->withErrors([
                'seller_request' => 'У вас уже есть заявка, которая ожидает решения администратора.',
            ]);
        }

        $validated = $request->validate([
            'message' => ['required', 'string', 'min:30', 'max:2000'],
        ]);

        SellerRequest::create([
            'user_id' => $user->id,
            'status' => 'pending',
            'message' => $validated['message'],
        ]);

        return redirect()->route('seller.request.create')->with('status', 'Заявка на статус продавца отправлена на модерацию.');
    }

    /**
     * Список заявок на статус продавца для администратора.
     */
    public function index(): View
    {
        $requests = SellerRequest::with(['user', 'reviewer'])
            ->orderByRaw("CASE WHEN status = 'pending' THEN 0 ELSE 1 END")
            ->orderByDesc('created_at')
            ->get();

        return view('admin.seller_requests.index', [
            'requests' => $requests,
        ]);
    }

    /**
     * Одобрение заявки администратором.
     */
    public function approveRequest(Request $request, SellerRequest $sellerRequest, RoleService $roleService): RedirectResponse
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

        $roleService->assignRole($sellerRequest->user, 'seller');

        return back()->with('status', 'Заявка одобрена. Пользователь получил доступ к кабинету продавца.');
    }

    /**
     * Отклонение заявки администратором.
     */
    public function rejectRequest(Request $request, SellerRequest $sellerRequest): RedirectResponse
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