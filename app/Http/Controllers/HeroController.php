<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Hero;
class HeroController extends Controller
{
    public function index()
    {
        // Ambil semua data hero dari database
        $heroes = Hero::all(); 
        
        // Kirim datanya ke halaman web (view) bernama 'hero_list'
        return view('hero_list', compact('heroes'));
    }
}