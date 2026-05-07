<?php

namespace App\Http\Controllers;

use App\Services\LibraryService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LibraryController extends Controller
{
    /**
     * Библиотека пользователя.
     */
    public function index(Request $request, LibraryService $libraryService): View
    {
        $user = $request->user();

        if (!$user) {
            abort(401, 'Unauthorized');
        }

        return view('library.index', [
            'library' => $libraryService->getUserLibrary($user),
        ]);
    }
}