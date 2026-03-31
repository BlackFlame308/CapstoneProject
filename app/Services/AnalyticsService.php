<?php

namespace App\Services;

use App\Models\Analytic;
use App\Models\Household;
use App\Models\Member;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class AnalyticsService
{
    public function getBarangayAnalytics(): array
    {
        $totalHouseholds = Household::count();
        $totalPopulation = Member::count();
        $totalSeniors = Member::where('age', '>=', 60)->count();
        $totalChildren = Member::where('age', '<=', 17)->count();
        $totalAdults = Member::whereBetween('age', [18, 59])->count();
        $totalPwd = Member::where('is_pwd', true)->count();

        return [
            'total_households' => $totalHouseholds,
            'total_population' => $totalPopulation,
            'total_seniors' => $totalSeniors,
            'total_children' => $totalChildren,
            'total_adults' => $totalAdults,
            'total_pwd' => $totalPwd,
        ];
    }

    public function getSitioAnalytics(): Collection
    {
        $sitioData = Household::select('sitio')
            ->whereNotNull('sitio')
            ->groupBy('sitio')
            ->get()
            ->pluck('sitio');

        return $sitioData->map(function ($sitio) {
            $households = Household::where('sitio', $sitio)->get();
            $memberCollection = Member::whereIn('household_id', $households->pluck('id'))->get();

            $population = $memberCollection->count();
            $seniors = $memberCollection->where('age', '>=', 60)->count();
            $children = $memberCollection->where('age', '<=', 17)->count();
            $adults = $memberCollection->whereBetween('age', [18, 59])->count();
            $pwd = $memberCollection->where('is_pwd', true)->count();

            $seniorPct = $population > 0 ? round(($seniors / $population) * 100, 2) : 0;
            $childrenPct = $population > 0 ? round(($children / $population) * 100, 2) : 0;
            $adultPct = $population > 0 ? round(($adults / $population) * 100, 2) : 0;
            $pwdPct = $population > 0 ? round(($pwd / $population) * 100, 2) : 0;

            // vulnerability score: PWD(3), children(2), seniors(1)
            $vulnerabilityScore = ($pwd * 3) + ($children * 2) + ($seniors * 1);

            return [
                'sitio' => $sitio,
                'households' => $households->count(),
                'population' => $population,
                'breakdown' => [
                    'senior_pct' => $seniorPct,
                    'children_pct' => $childrenPct,
                    'adult_pct' => $adultPct,
                    'pwd_pct' => $pwdPct,
                ],
                'vulnerability' => [
                    'pwd' => $pwd,
                    'children' => $children,
                    'seniors' => $seniors,
                ],
                'vulnerability_score' => $vulnerabilityScore,
            ];
        })->sortByDesc('vulnerability_score')->values();
    }

    public function refreshCachedAnalytics(): void
    {
        $barangay = $this->getBarangayAnalytics();
        Analytic::updateOrCreate(
            ['type' => 'barangay', 'sitio' => null],
            $barangay
        );

        $sitioAnalytics = $this->getSitioAnalytics();

        foreach ($sitioAnalytics as $stat) {
            Analytic::updateOrCreate(
                ['type' => 'sitio', 'sitio' => $stat['sitio']],
                [
                    'total_households' => $stat['households'],
                    'total_population' => $stat['population'],
                    'total_seniors' => $stat['vulnerability']['seniors'],
                    'total_pwd' => $stat['vulnerability']['pwd'],
                    'total_children' => $stat['vulnerability']['children'],
                    'total_adults' => $stat['breakdown']['adult_pct'],
                ]
            );
        }
    }
}
