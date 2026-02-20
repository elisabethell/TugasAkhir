<?php
// app/Models/Hero.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Hero extends Model
{
    protected $table = 'master_heroes';
    public $timestamps = false;
    
    protected $fillable = [
        'nama_hero', 'role_1', 'role_2', 'damage_source', 'damage_output',
        'spec_1', 'spec_2', 'lane_recommendation_1', 'lane_recommendation_2'
    ];

    // Power spike: Early, Mid, Late
    public function getPowerSpikeAttribute()
    {
        $spikes = [
            'Ling' => 'Late',
            'Fanny' => 'Early-Mid',
            'Chou' => 'Early',
            'Beatrix' => 'Mid-Late',
            'Atlas' => 'Mid',
            'Valentina' => 'Mid',
            'Arlott' => 'Early-Mid',
            'Joy' => 'Early',
            'Granger' => 'Mid',
            'Lunox' => 'Late',
            'Aldous' => 'Late',
            'X.Borg' => 'Early-Mid',
        ];
        
        return $spikes[$this->nama_hero] ?? 'Mid';
    }
    
    // Win condition specialty
    public function getWinConditionAttribute()
    {
        $conditions = [
            'Ling' => 'Split Push',
            'Atlas' => 'Team Fight',
            'Chou' => 'Pick Off',
            'Fanny' => 'Pick Off',
            'Beatrix' => 'Team Fight',
            'Valentina' => 'Team Fight',
            'Arlott' => 'Pick Off',
            'Joy' => 'Pick Off',
            'Aldous' => 'Split Push',
            'X.Borg' => 'Team Fight',
            'Franco' => 'Pick Off',
            'Mathilda' => 'Team Fight',
        ];
        
        return $conditions[$this->nama_hero] ?? 'Team Fight';
    }
    
    // Team fight strength
    public function getTeamFightAttribute()
    {
        $strength = [
            'Atlas' => 'S',
            'Tigreal' => 'S',
            'Valentina' => 'A',
            'Beatrix' => 'A',
            'Ling' => 'B',
            'Fanny' => 'C',
            'Chou' => 'B',
            'Arlott' => 'A',
            'Joy' => 'B',
        ];
        
        return $strength[$this->nama_hero] ?? 'B';
    }
    
    // Pushing power
    public function getPushPowerAttribute()
    {
        $power = [
            'Ling' => 'A',
            'Zilong' => 'S',
            'Sun' => 'S',
            'Masha' => 'S',
            'Beatrix' => 'B',
            'Granger' => 'B',
            'X.Borg' => 'A',
        ];
        
        return $power[$this->nama_hero] ?? 'B';
    }
}