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
}
