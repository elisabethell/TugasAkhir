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

    protected $appends = [
        'formatted_date_time',
        'simple_date',
        'simple_time',
        'formatted_stage_name',
        'winner',
        'team_a_name',
        'team_b_name',
    ];

    public function getTeamANameAttribute(): ?string
    {
        return $this->team_1_name;
    }

    public function getTeamBNameAttribute(): ?string
    {
        return $this->team_2_name;
    }

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
     * Contoh: "8 Nov 2026 - 17:30 WIB"
     */
    public function getFormattedDateTimeAttribute(): string
    {
        if (empty($this->match_date)) {
            return 'Date TBA';
        }

        try {
            $dt = $this->match_date instanceof Carbon
                ? $this->match_date->copy()->setTimezone('Asia/Jakarta')
                : Carbon::parse($this->match_date)->setTimezone('Asia/Jakarta');
            return $dt->translatedFormat('d M Y - H:i') . ' WIB';
        } catch (\Throwable $e) {
            return 'Date TBA';
        }
    }

    /**
     * Format tanggal ringkas (Contoh: "08 Nov 2026")
     */
    public function getSimpleDateAttribute(): string
    {
        if (empty($this->match_date)) {
            return 'Date TBA';
        }

        try {
            $dt = $this->match_date instanceof Carbon
                ? $this->match_date->copy()->setTimezone('Asia/Jakarta')
                : Carbon::parse($this->match_date)->setTimezone('Asia/Jakarta');
            return $dt->translatedFormat('d M Y');
        } catch (\Throwable $e) {
            return 'Date TBA';
        }
    }

    /**
     * Format jam ringkas WIB (Contoh: "17:30 WIB")
     */
    public function getSimpleTimeAttribute(): string
    {
        if (empty($this->match_date)) {
            return '-';
        }

        try {
            $dt = $this->match_date instanceof Carbon
                ? $this->match_date->copy()->setTimezone('Asia/Jakarta')
                : Carbon::parse($this->match_date)->setTimezone('Asia/Jakarta');
            return $dt->format('H:i') . ' WIB';
        } catch (\Throwable $e) {
            return '-';
        }
    }

    /**
     * Resolusi Nama Babak Pertandingan (Grand Final vs Lower Bracket Final vs Babak lainnya)
     */
    public function getFormattedStageNameAttribute(): string
    {
        $raw = trim($this->stage_name ?? '');
        $mid = strtoupper($this->match_id ?? '');

        // 1. Pola Match ID spesifik Liquipedia Bracket (Prioritas Utama untuk akurasi babak):
        // R04-M001 = Round 4 Match 1 = Grand Final
        if (str_contains($mid, 'R04') || str_contains($mid, 'GF')) {
            return 'Grand Final Playoffs';
        }
        // R03-M002 = Round 3 Match 2 = Lower Bracket Final
        if (str_contains($mid, 'R03-M002') || str_contains($mid, 'R03_M002') || str_contains($mid, 'LBF')) {
            return 'Lower Bracket Final';
        }
        // R03-M001 = Round 3 Match 1 = Upper Bracket Final
        if (str_contains($mid, 'R03-M001') || str_contains($mid, 'R03_M001') || str_contains($mid, 'UBF')) {
            return 'Upper Bracket Final';
        }
        if (str_contains($mid, 'R02-M002')) {
            return 'Lower Bracket Semifinal';
        }
        if (str_contains($mid, 'R02-M001')) {
            return 'Upper Bracket Semifinal';
        }
        if (str_contains($mid, 'R01')) {
            return 'Playoffs Round 1';
        }

        // 2. Cek jika sudah eksplisit tercantum di stage_name
        if (str_contains(strtolower($raw), 'lower bracket final')) {
            return 'Lower Bracket Final';
        }
        if (str_contains(strtolower($raw), 'upper bracket final')) {
            return 'Upper Bracket Final';
        }
        if (str_contains(strtolower($raw), 'grand final')) {
            return 'Grand Final Playoffs';
        }

        // 3. Pola Regular Season dengan Nomor Week (Contoh: "Week 1 Regular Season", "Week 3 Regular Season")
        // Pola dari stage_name / raw text (misal: "Week 2", "Regular Season Week 3")
        if (preg_match('/Week\s*(\d+)/i', $raw, $matches)) {
            $weekNum = (int) $matches[1];
            return "Week {$weekNum} Regular Season";
        }
        // Pola dari match_id: W01, W02, W1, W8, RS_W02, dll.
        if (preg_match('/(?:^|[^A-Z0-9])(?:W|WEEK)[-_ ]?0?([1-9]\d?)(?:[^A-Z0-9]|$)/i', $mid, $matches)) {
            $weekNum = (int) $matches[1];
            return "Week {$weekNum} Regular Season";
        }
        // Pola dari tournament_name
        $tourney = $this->tournament_name ?? '';
        if (preg_match('/Week\s*(\d+)/i', $tourney, $matches)) {
            $weekNum = (int) $matches[1];
            return "Week {$weekNum} Regular Season";
        }
        if (str_contains(strtolower($raw), 'regular') || str_contains(strtolower($mid), 'rs')) {
            return 'Week 1 Regular Season';
        }

        return !empty($raw) ? $raw : 'Playoffs';
    }

    /**
     * Resolusi Logo Tim 1 (Memastikan logo resmi SVG lokal / Liquipedia termuat tanpa 403)
     */
    public function getTeam1LogoAttribute($value): string
    {
        if (!empty($this->team_1_name)) {
            return app(\App\Services\LiquipediaApiService::class)->resolveTeamLogo($this->team_1_name, $value);
        }
        return $value ?? '';
    }

    /**
     * Resolusi Logo Tim 2 (Memastikan logo resmi SVG lokal / Liquipedia termuat tanpa 403)
     */
    public function getTeam2LogoAttribute($value): string
    {
        if (!empty($this->team_2_name)) {
            return app(\App\Services\LiquipediaApiService::class)->resolveTeamLogo($this->team_2_name, $value);
        }
        return $value ?? '';
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
