<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Services\AnalyticsService;
use Illuminate\Http\JsonResponse;

class AnalyticController extends Controller
{
    private AnalyticsService $analyticsService;

    public function __construct(AnalyticsService $analyticsService)
    {
        $this->analyticsService = $analyticsService;
    }

    public function barangay(): JsonResponse
    {
        $data = $this->analyticsService->getBarangayAnalytics();

        return response()->json([
            'status' => 'success',
            'data' => $data,
            'message' => 'Barangay analytics',
        ], 200);
    }

    public function sitio(): JsonResponse
    {
        $data = $this->analyticsService->getSitioAnalytics();

        return response()->json([
            'status' => 'success',
            'data' => $data,
            'message' => 'Sitio analytics',
        ], 200);
    }

    public function refresh(): JsonResponse
    {
        $this->analyticsService->refreshCachedAnalytics();

        return response()->json([
            'status' => 'success',
            'data' => null,
            'message' => 'Analytics refreshed',
        ], 200);
    }
}
