<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Dataset;
use Carbon\Carbon;

class DatasetSeeder extends Seeder
{
    public function run(): void
    {
        $file = fopen(database_path('data/MWI_X_EWC_2026.csv'), 'r');
        $isHeader = true;
        
        // 1. Ubah koma (',') menjadi titik koma (';') di sini
        while (($data = fgetcsv($file, 2000, ';')) !== false) {
            if ($isHeader) { 
                $isHeader = false; 
                continue; 
            }
            
            // Lewati jika baris kosong atau datanya kurang dari 17 kolom
            if (count($data) < 17) {
                continue;
            }
            
            // 2. Sesuaikan format tanggal menjadi 'd/m/Y' (menggunakan garis miring)
            try {
                $formattedDate = Carbon::createFromFormat('d/m/Y', trim($data[0]))->format('Y-m-d');
            } catch (\Exception $e) {
                $formattedDate = Carbon::parse(trim($data[0]))->format('Y-m-d');
            }

            Dataset::create([
                'date'        => $formattedDate,
                'side'        => trim($data[1]),
                'win_lose'    => trim($data[2]),
                'player'      => trim($data[3]),
                'role'        => trim($data[4]),
                'hero'        => trim($data[5]),
                'hero_ban'    => empty($data[6]) ? null : trim($data[6]),
                'spell'       => trim($data[7]),
                'team'        => trim($data[8]),
                'opponent'    => trim($data[9]),
                'skor_game'   => trim($data[10]),
                'kill_stat'   => (int)$data[11],
                'death_stat'  => (int)$data[12],
                'assist_stat' => (int)$data[13],
                'duration'    => trim($data[14]),
                'map'         => trim($data[15]),
                'bracket'     => trim($data[16]),
            ]);
        }
        fclose($file);
    }
}