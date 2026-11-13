<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\RbrRule;

class RbrRuleController extends Controller
{
    /**
     * Halaman Khusus Dosen: Rule Inspector & Verifikasi Sidang Skripsi
     */
    public function index(Request $request)
    {
        // Pastikan tabel dan basis aturan akademik terinisialisasi
        RbrRule::seedDefaultRules();

        $category = $request->query('category', 'all');
        $query = RbrRule::orderBy('id_rule');

        if ($category !== 'all') {
            $query->where('category', $category);
        }

        $rules = $query->get();

        $allRulesCount = RbrRule::count();
        $synergyCount = RbrRule::where('category', 'Synergy')->count();
        $counterCount = RbrRule::where('category', 'Counter')->count();
        $powerSpikeCount = RbrRule::where('category', 'PowerSpike')->count();
        $compositionCount = RbrRule::where('category', 'Composition')->count();

        return view('rbr_rules', compact(
            'rules',
            'category',
            'allRulesCount',
            'synergyCount',
            'counterCount',
            'powerSpikeCount',
            'compositionCount'
        ));
    }
}
