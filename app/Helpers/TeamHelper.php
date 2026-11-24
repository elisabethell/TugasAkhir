<?php

if (!function_exists('getTeamLogo')) {
    /**
     * Helper untuk mendapatkan path logo tim lokal di folder public/images/teams/
     * Menghapus seluruh logika auto-generate SVG / ui-avatars.
     *
     * @param string|null $teamName
     * @return string
     */
    function getTeamLogo(?string $teamName): string
    {
        if (empty($teamName)) {
            return '';
        }

        $clean = strtolower(trim(str_replace(['[[', ']]', '_'], ['', '', ' '], $teamName)));

        return match (true) {
            str_contains($clean, 'evos') => '/images/teams/evos.png',
            str_contains($clean, 'rrq') => '/images/teams/rrq.png',
            str_contains($clean, 'navi') || str_contains($clean, 'natus') => '/images/teams/navi.png',
            str_contains($clean, 'alter ego') || $clean === 'ae' || str_starts_with($clean, 'ae ') => '/images/teams/ae.png',
            str_contains($clean, 'tlid') || str_contains($clean, 'liquid') => '/images/teams/tlid.png',
            str_contains($clean, 'geek') => '/images/teams/geek.png',
            str_contains($clean, 'dewa') => '/images/teams/dewa.png',
            str_contains($clean, 'btr') || str_contains($clean, 'bigetron') || str_contains($clean, 'vitality') => '/images/teams/btr.png',
            str_contains($clean, 'onic') => '/images/teams/onic.png',
            default => file_exists(public_path('images/teams/' . $clean . '.png'))
                ? '/images/teams/' . $clean . '.png'
                : '',
        };
    }
}
