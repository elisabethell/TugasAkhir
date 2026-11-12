<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Carbon\Carbon;

class MatchDataset extends Model
{
    use HasFactory;

    protected $table = 'match_datasets';
    protected $guarded = ['id'];

    protected $casts = [
        'match_date'   => 'datetime',
        'team_1_score' => 'integer',
        'team_2_score' => 'integer',
    ];

    /**
     * Relasi ke game-game individual di dalam seri pertandingan (Bo3 / Bo5 / Bo7)
     */
    public function games(): HasMany
    {
        return $this->hasMany(MatchGame::class, 'match_dataset_id')->orderBy('game_number', 'asc');
    }

    /**
     * Relasi ke seluruh hero picks & bans di seluruh seri
     */
    public function details(): HasMany
    {
        return $this->hasMany(MatchDetail::class, 'match_id', 'match_id');
    }

    /**
     * Format tanggal dan jam WIB sesuai standar tampilan Liquipedia:
     * Contoh: "November 4, 2026 - 14:30 WIB"
     */
    public function getFormattedDateTimeAttribute(): string
    {
        if (!$this->match_date) {
            return 'Date TBA';
        }

        $dt = Carbon::parse($this->match_date)->setTimezone('Asia/Jakarta');
        return $dt->translatedFormat('F j, Y - H:i') . ' WIB';
    }

    /**
     * Status pemenang seri
     */
    public function getWinnerAttribute(): ?string
    {
        if ($this->team_1_score > $this->team_2_score) {
            return $this->team_1_name;
        } elseif ($this->team_2_score > $this->team_1_score) {
            return $this->team_2_name;
        }
        return null;
    }
}
