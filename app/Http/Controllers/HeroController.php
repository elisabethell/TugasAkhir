<?php

namespace App\Http\Controllers;

use App\Models\Hero;
use Illuminate\Http\Request;

class HeroController extends Controller
{
    public function index()
    {
        $heroes = Hero::all(); // Ambil semua data dari database
        return view('heroes.index', compact('heroes'));
    }
}