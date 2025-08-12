<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $posts = Auth::user()->posts()->latest()->paginate(10);
        return view('dashboard', compact('posts'));
    }
}