<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /**
     * Регистрация пользователя.
     */
    public function register(Request $request, ActivityLogger $activityLogger)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        // Пароль хешируется через bcrypt.
        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        // Автоматический вход после регистрации.
        Auth::login($user);
        $activityLogger->logLogin($user);

        return redirect('/');
    }

    /**
     * Авторизация пользователя.
     */
    public function login(Request $request, ActivityLogger $activityLogger)
    {
        $credentials = $request->validate([
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ]);

        if (!Auth::attempt($credentials)) {
            return back()->withErrors([
                'email' => 'Неверный email или пароль.',
            ]);
        }

        $request->session()->regenerate();

        $activityLogger->logLogin($request->user());

        return redirect('/');
    }

    /**
     * Выход пользователя.
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
