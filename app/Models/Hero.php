<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Hero extends Model
{
    use HasFactory;

    protected $table = 'heroes';
    protected $guarded = [];

    // Otomatis ubah format JSON dari dataset menjadi array PHP
    protected $casts = [
        'laning' => 'array',
        'skills' => 'array',
        'specialty' => 'array',
        'counters' => 'array',
        'synergies' => 'array',
    ];
}