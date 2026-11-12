<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MatchGame extends Model
{
    use HasFactory;

    protected $table = 'match_games';
    protected $guarded = ['id'];

    protected $casts = [
        'game_number' => 'integer',
    ];

    /**
     * Relasi parent ke Seri Pertandingan
     */
    public function dataset(): BelongsTo
    {
        return $this->belongsTo(MatchDataset::class, 'match_dataset_id');
    }

    /**
     * Relasi ke seluruh hero picks & bans di game ini
     */
    public function details(): HasMany
    {
        return $this->hasMany(MatchDetail::class, 'match_game_id')->orderBy('slot_number', 'asc');
    }

    /**
     * Helper 5 Hero Picks Tim 1 (Kiri)
     */
    public function team1Picks()
    {
        return $this->details->where('type', 'pick')->where('team_position', 1)->values();
    }

    /**
     * Helper 5 Hero Picks Tim 2 (Kanan)
     */
    public function team2Picks()
    {
        return $this->details->where('type', 'pick')->where('team_position', 2)->values();
    }

    /**
     * Helper 5 Hero Bans Tim 1 (Kiri)
     */
    public function team1Bans()
    {
        return $this->details->where('type', 'ban')->where('team_position', 1)->values();
    }

    /**
     * Helper 5 Hero Bans Tim 2 (Kanan)
     */
    public function team2Bans()
    {
        return $this->details->where('type', 'ban')->where('team_position', 2)->values();
    }
}
