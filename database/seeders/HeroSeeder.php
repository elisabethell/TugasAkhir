<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Hero;

class HeroSeeder extends Seeder
{
    public function run(): void
    {
        $path = database_path('data/data_hero.csv');
        
        if (!file_exists($path)) {
            return;
        }

        $file = fopen($path, 'r');
        $isHeader = true;
        
        while (($data = fgetcsv($file, 10000, ',')) !== false) {
            if ($isHeader) { 
                $isHeader = false; 
                continue; 
            }

            if (!isset($data[2]) || empty(trim($data[2]))) {
                continue;
            }
            
            $clean = function($val) {
                return trim(str_replace(['[', ']', "'", '"'], '', $val));
            };

            // Membersihkan teks dari masalah karakter UTF-8 yang rusak
            $cleanUtf8 = function($val) {
                return mb_convert_encoding($val, 'UTF-8', 'UTF-8');
            };

            Hero::create([
                'hero_id'   => trim($data[1] ?? ''),
                'hero_name' => trim($data[2] ?? ''),
                'portrait'  => trim($data[3] ?? ''),
                'laning'    => $clean($data[4] ?? ''),
                'class'     => $clean($data[5] ?? ''),
                'skills'    => $cleanUtf8($data[6] ?? ''),
                'specialty' => $clean($data[7] ?? ''),
                'counters'  => $data[8] ?? '',
                'synergies' => $data[9] ?? '',
            ]);
        }
        
        fclose($file);
    }
}