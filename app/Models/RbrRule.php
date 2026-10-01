<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RbrRule extends Model
{
    protected $primaryKey = 'id_rule';
    protected $fillable = [
        'kondisi_if',
        'kesimpulan_then',
        'status_aktif',
    ];
}
