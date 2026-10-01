<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\RbrRule;

class RbrRuleController extends Controller
{
    public function index()
    {
        $rules = RbrRule::orderBy('id_rule')->get();
        return view('rbr_rules', compact('rules'));
    }
}
