<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Hero extends Model
{
    use HasFactory;

    protected $table = 'heroes';

    protected $fillable = [
        'hero_id',
        'hero_name',
        'portrait',
        'laning',
        'class',
        'skills',
        'specialty',
        'counters',
        'synergies',
        'role_1',
        'role_2',
        'lane_1',
        'lane_2',
        'sustainability',
        'attack_power',
        'crowd_control',
        'mobility',
        'lane_control',
    ];

    // Otomatis ubah format JSON dari dataset menjadi array PHP
    protected $casts = [
        'laning'         => 'array',
        'skills'         => 'array',
        'specialty'      => 'array',
        'counters'       => 'array',
        'synergies'      => 'array',
        'sustainability' => 'integer',
        'attack_power'   => 'integer',
        'crowd_control'  => 'integer',
        'mobility'       => 'integer',
        'lane_control'   => 'integer',
    ];
}