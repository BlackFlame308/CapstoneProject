<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreDisasterEventRequest;
use App\Http\Requests\UpdateDisasterEventRequest;
use App\Models\DisasterEvent;
use Illuminate\Http\JsonResponse;

class DisasterEventController extends Controller
{
    public function index(): JsonResponse
    {
        $events = DisasterEvent::with('reports')->paginate(20);

        return response()->json([
            'status' => 'success',
            'data' => $events,
            'message' => 'Disaster events retrieved',
        ], 200);
    }

    public function store(StoreDisasterEventRequest $request): JsonResponse
    {
        $event = DisasterEvent::create([
            'name' => $request->input('name'),
            'disaster_type' => $request->input('disaster_type'),
            'date' => $request->input('date'),
            'description' => $request->input('description'),
        ]);

        return response()->json([
            'status' => 'success',
            'data' => $event,
            'message' => 'Disaster event created',
        ], 201);
    }

    public function show(DisasterEvent $disasterEvent): JsonResponse
    {
        $disasterEvent->load('reports');

        return response()->json([
            'status' => 'success',
            'data' => $disasterEvent,
            'message' => 'Disaster event retrieved',
        ], 200);
    }

    public function update(UpdateDisasterEventRequest $request, DisasterEvent $disasterEvent): JsonResponse
    {
        $disasterEvent->update([
            'name' => $request->input('name'),
            'disaster_type' => $request->input('disaster_type'),
            'date' => $request->input('date'),
            'description' => $request->input('description'),
        ]);

        return response()->json([
            'status' => 'success',
            'data' => $disasterEvent,
            'message' => 'Disaster event updated',
        ], 200);
    }

    public function destroy(DisasterEvent $disasterEvent): JsonResponse
    {
        $disasterEvent->delete();

        return response()->json([
            'status' => 'success',
            'data' => null,
            'message' => 'Disaster event deleted',
        ], 200);
    }
}
