<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MatchDetail extends Model
{
    use HasFactory;

    protected $table = 'match_details';
    protected $guarded = ['id'];

    protected $casts = [
        'game_number'   => 'integer',
        'slot_number'   => 'integer',
        'team_position' => 'integer',
    ];

    /**
     * Relasi ke parent game
     */
    public function game(): BelongsTo
    {
        return $this->belongsTo(MatchGame::class, 'match_game_id');
    }

    /**
     * Relasi ke model Hero
     */
    public function hero(): BelongsTo
    {
        return $this->belongsTo(Hero::class, 'hero_name', 'hero_name');
    }

    /**
     * Accessor portrait hero dari database/data/data_hero.csv
     */
    public function getHeroPortraitAttribute($value): string
    {
        if (!empty($value) && str_starts_with($value, 'http') && !str_contains($value, 'ui-avatars.com')) {
            return $value;
        }

        if (!empty($this->hero_name)) {
            return app(\App\Services\CsvDataService::class)->resolveHeroPortrait($this->hero_name);
        }

        return $value ?? '';
    }
}
