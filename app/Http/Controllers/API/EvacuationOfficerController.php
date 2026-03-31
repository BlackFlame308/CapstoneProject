<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreEvacuationOfficerRequest;
use App\Http\Requests\UpdateEvacuationOfficerRequest;
use App\Models\EvacuationOfficer;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;

class EvacuationOfficerController extends Controller
{
    public function index(): JsonResponse
    {
        $officers = EvacuationOfficer::paginate(20);

        return response()->json([
            'status' => 'success',
            'data' => $officers,
            'message' => 'Evacuation officers retrieved',
        ], 200);
    }

    public function store(StoreEvacuationOfficerRequest $request): JsonResponse
    {
        $officer = EvacuationOfficer::create([
            'name' => $request->input('name'),
            'officer_id' => 'EVACO-' . strtoupper(Str::random(8)),
            'contact_number' => $request->input('contact_number'),
            'assigned_area' => $request->input('assigned_area'),
            'address' => $request->input('address'),
        ]);

        return response()->json([
            'status' => 'success',
            'data' => $officer,
            'message' => 'Evacuation officer created',
        ], 201);
    }

    public function show(EvacuationOfficer $evacuationOfficer): JsonResponse
    {
        return response()->json([
            'status' => 'success',
            'data' => $evacuationOfficer,
            'message' => 'Evacuation officer retrieved',
        ], 200);
    }

    public function update(UpdateEvacuationOfficerRequest $request, EvacuationOfficer $evacuationOfficer): JsonResponse
    {
        $evacuationOfficer->update([
            'name' => $request->input('name'),
            'contact_number' => $request->input('contact_number'),
            'assigned_area' => $request->input('assigned_area'),
            'address' => $request->input('address'),
        ]);

        return response()->json([
            'status' => 'success',
            'data' => $evacuationOfficer,
            'message' => 'Evacuation officer updated',
        ], 200);
    }

    public function destroy(EvacuationOfficer $evacuationOfficer): JsonResponse
    {
        $evacuationOfficer->delete();

        return response()->json([
            'status' => 'success',
            'data' => null,
            'message' => 'Evacuation officer deleted',
        ], 200);
    }
}
