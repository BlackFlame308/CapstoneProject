<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreResponderRequest;
use App\Http\Requests\UpdateResponderRequest;
use App\Models\Responder;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;

class ResponderController extends Controller
{
    public function index(): JsonResponse
    {
        $responders = Responder::paginate(20);

        return response()->json([
            'status' => 'success',
            'data' => $responders,
            'message' => 'Responders retrieved',
        ], 200);
    }

    public function store(StoreResponderRequest $request): JsonResponse
    {
        $responder = Responder::create([
            'name' => $request->input('name'),
            'responder_id' => 'RESP-' . strtoupper(Str::random(8)),
            'contact_number' => $request->input('contact_number'),
            'assigned_area' => $request->input('assigned_area'),
            'address' => $request->input('address'),
        ]);

        return response()->json([
            'status' => 'success',
            'data' => $responder,
            'message' => 'Responder created',
        ], 201);
    }

    public function show(Responder $responder): JsonResponse
    {
        return response()->json([
            'status' => 'success',
            'data' => $responder,
            'message' => 'Responder retrieved',
        ], 200);
    }

    public function update(UpdateResponderRequest $request, Responder $responder): JsonResponse
    {
        $responder->update([
            'name' => $request->input('name'),
            'contact_number' => $request->input('contact_number'),
            'assigned_area' => $request->input('assigned_area'),
            'address' => $request->input('address'),
        ]);

        return response()->json([
            'status' => 'success',
            'data' => $responder,
            'message' => 'Responder updated',
        ], 200);
    }

    public function destroy(Responder $responder): JsonResponse
    {
        $responder->delete();

        return response()->json([
            'status' => 'success',
            'data' => null,
            'message' => 'Responder deleted',
        ], 200);
    }
}
