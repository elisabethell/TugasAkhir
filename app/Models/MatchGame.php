<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class MatchGame extends Model
{
    use HasFactory;

    protected $table = 'match_games';
    protected $guarded = ['id'];

    protected $casts = [
        'game_number'  => 'integer',
        'team_1_picks' => 'array',
        'team_2_picks' => 'array',
        'team_1_bans'  => 'array',
        'team_2_bans'  => 'array',
    ];

    protected $appends = [
        'winner',
    ];

    /**
     * Memastikan kolom winner, team_1_picks, team_2_picks, team_1_bans, team_2_bans
     * tersedia di database MySQL secara dinamis.
     */
    public static function ensureColumnsExist(): void
    {
        try {
            if (!Schema::hasTable('match_games')) return;

            Schema::table('match_games', function (Blueprint $table) {
                if (!Schema::hasColumn('match_games', 'winner')) {
                    $table->string('winner')->nullable()->after('map_type');
                }
                if (!Schema::hasColumn('match_games', 'team_1_picks')) {
                    $table->longText('team_1_picks')->nullable()->after('team_2_side');
                }
                if (!Schema::hasColumn('match_games', 'team_2_picks')) {
                    $table->longText('team_2_picks')->nullable()->after('team_1_picks');
                }
                if (!Schema::hasColumn('match_games', 'team_1_bans')) {
                    $table->longText('team_1_bans')->nullable()->after('team_2_picks');
                }
                if (!Schema::hasColumn('match_games', 'team_2_bans')) {
                    $table->longText('team_2_bans')->nullable()->after('team_1_bans');
                }
            });
        } catch (\Throwable $e) {
            // Ignore schema alter errors if columns already exist or permission restricted
        }
    }

    /**
     * Relasi parent ke Seri Pertandingan
     */
    public function dataset(): BelongsTo
    {
        return $this->belongsTo(MatchDataset::class, 'match_dataset_id');
    }

    public function match(): BelongsTo
    {
        return $this->belongsTo(MatchDataset::class, 'match_dataset_id');
    }

    /**
     * Relasi ke seluruh hero picks & bans di game ini (tabel match_details)
     */
    public function details(): HasMany
    {
        return $this->hasMany(MatchDetail::class, 'match_game_id')->orderBy('slot_number', 'asc');
    }

    /**
     * Accessor winner: mengembalikan winner_team jika winner kosong
     */
    public function getWinnerAttribute($value): ?string
    {
        return $value ?: ($this->attributes['winner_team'] ?? null);
    }

    /**
     * Accessor team_1_picks: jika kolom null, fallback ke relasi match_details
     */
    public function getTeam1PicksAttribute($value): array
    {
        if (!empty($value)) {
            return is_array($value) ? $value : (json_decode($value, true) ?: []);
        }

        if ($this->relationLoaded('details')) {
            return $this->details->where('type', 'pick')->where('team_position', 1)->pluck('hero_name')->values()->all();
        }

        return [];
    }

    /**
     * Accessor team_2_picks: jika kolom null, fallback ke relasi match_details
     */
    public function getTeam2PicksAttribute($value): array
    {
        if (!empty($value)) {
            return is_array($value) ? $value : (json_decode($value, true) ?: []);
        }

        if ($this->relationLoaded('details')) {
            return $this->details->where('type', 'pick')->where('team_position', 2)->pluck('hero_name')->values()->all();
        }

        return [];
    }

    /**
     * Accessor team_1_bans: jika kolom null, fallback ke relasi match_details
     */
    public function getTeam1BansAttribute($value): array
    {
        if (!empty($value)) {
            return is_array($value) ? $value : (json_decode($value, true) ?: []);
        }

        if ($this->relationLoaded('details')) {
            return $this->details->where('type', 'ban')->where('team_position', 1)->pluck('hero_name')->values()->all();
        }

        return [];
    }

    /**
     * Accessor team_2_bans: jika kolom null, fallback ke relasi match_details
     */
    public function getTeam2BansAttribute($value): array
    {
        if (!empty($value)) {
            return is_array($value) ? $value : (json_decode($value, true) ?: []);
        }

        if ($this->relationLoaded('details')) {
            return $this->details->where('type', 'ban')->where('team_position', 2)->pluck('hero_name')->values()->all();
        }

        return [];
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
