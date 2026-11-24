<?php

namespace App\Services;

/**
 * DraftRecommendationService
 * 
 * Mesin Rekomendasi Draft Pick & Ban (ARM & RBR)
 * Mendukung analisis Draft DNA berbasis data historis spesifik tim.
 */
class DraftRecommendationService extends DraftInferenceService
{
    /**
     * Menganalisis profil Draft DNA spesifik tim (MPL ID Season 18)
     */
    public function getTeamDraftDna(?string $teamName): ?array
    {
        return $this->teamDnaService->getTeamDraftDna($teamName);
    }

    /**
     * Mengambil daftar profil Draft DNA seluruh tim peserta MPL ID Season 18
     */
    public function getAllTeamsDna(): array
    {
        return $this->teamDnaService->getAllTeamsDna();
    }

    /**
     * Mencari 2-3 pertandingan asli turnamen MPL ID S18 yang paling mirip dengan draft saat ini
     */
    public function getSimilarMatches(array $currentPicks = [], ?string $teamName = 'Alter Ego', int $limit = 3): array
    {
        return $this->teamDnaService->getSimilarMatches($currentPicks, $teamName, $limit);
    }
}
