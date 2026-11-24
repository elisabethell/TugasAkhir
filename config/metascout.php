<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Bobot Skor Hybrid Inferensi Rekomendasi Draft
    |--------------------------------------------------------------------------
    |
    | total_score = bobot_data * data_score + bobot_rule * rule_score
    | Bobot ini dapat disesuaikan untuk eksperimen skripsi antara pendekatan
    | Data-Driven (statistik turnamen) dan Expert-Driven (Rule-Based Reasoning).
    |
    */
    'weights' => [
        'data' => 0.50, // Bobot Data-Driven (50%)
        'rule' => 0.50, // Bobot Expert-Driven / RBR (50%)
    ],

    /*
    |--------------------------------------------------------------------------
    | Parameter Bayesian Smoothing (M-Estimate / Shrinkage)
    |--------------------------------------------------------------------------
    |
    | smoothed_wr = (wins + k * prior) / (n + k)
    | Mencegah fluktuasi ekstrem pada hero dengan sample size kecil (n kecil).
    |
    */
    'bayesian' => [
        'k'     => 10,    // Bobot prior shrinkage (sample equivalent)
        'prior' => 0.50,  // Prior win rate awal (50.0%)
    ],

    /*
    |--------------------------------------------------------------------------
    | Ambang Batas Komposisi & Reliabilitas Data
    |--------------------------------------------------------------------------
    |
    | Batas minimal jumlah pertandingan turnamen untuk dinyatakan valid dan
    | ambang batas defisit magical damage untuk mengaktifkan aturan mutlak.
    |
    */
    'thresholds' => [
        'low_data_n'                  => 10,    // Sample size < 10 diberi label low_data (data terbatas)
        'magic_deficit_threshold'     => 15.0,  // Persentase magic damage di bawah ini memicu rule Mage Wajib
        'physical_overload_threshold' => 80.0,  // Monotype fisik > 80% rentan counter Blade Armor
    ],
];
