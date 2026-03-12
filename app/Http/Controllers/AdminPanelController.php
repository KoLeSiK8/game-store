<?php

namespace App\Http\Controllers;

class AdminPanelController extends Controller
{
    /**
     * Главная страница админ-панели.
     */
    public function index()
    {
        return view('admin.index');
    }
}