<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreReportRequest;
use App\Http\Requests\UpdateReportRequest;
use App\Models\Report;
use Illuminate\Http\JsonResponse;

class ReportController extends Controller
{
    public function index(): JsonResponse
    {
        $reports = Report::paginate(20);

        return response()->json([
            'status' => 'success',
            'data' => $reports,
            'message' => 'Reports retrieved',
        ], 200);
    }

    public function store(StoreReportRequest $request): JsonResponse
    {
        $report = Report::create([
            'type' => $request->input('type'),
            'content' => $request->input('content'),
        ]);

        return response()->json([
            'status' => 'success',
            'data' => $report,
            'message' => 'Report created',
        ], 201);
    }

    public function show(Report $report): JsonResponse
    {
        return response()->json([
            'status' => 'success',
            'data' => $report,
            'message' => 'Report retrieved',
        ], 200);
    }

    public function update(UpdateReportRequest $request, Report $report): JsonResponse
    {
        $report->update([
            'type' => $request->input('type'),
            'content' => $request->input('content'),
        ]);

        return response()->json([
            'status' => 'success',
            'data' => $report,
            'message' => 'Report updated',
        ], 200);
    }

    public function destroy(Report $report): JsonResponse
    {
        $report->delete();

        return response()->json([
            'status' => 'success',
            'data' => null,
            'message' => 'Report deleted',
        ], 200);
    }
}
