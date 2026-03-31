<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function register(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|string|in:Captain,Encoder,Responder,Evacuation Officer',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation error',
                'errors' => $validator->errors(),
            ], 422);
        }

        $roleLookup = [
            'Captain' => 'Captain',
            'Encoder' => 'Encoder',
            'Responder' => 'Responder',
            'Evacuation Officer' => 'Evacuation Officer',
        ];

        $requestedRole = $request->input('role');
        $role = Role::firstWhere('name', $roleLookup[$requestedRole] ?? '');

        if (!$role) {
            return response()->json([
                'status' => 'error',
                'message' => 'Role not found',
            ], 404);
        }

        $currentUser = auth()->user();

        // Only Captain can create non-Captain users.
        if ($requestedRole !== 'Captain') {
            if (!$currentUser || !$currentUser->isSuperAdmin()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Forbidden: only Captain can register users with this role.',
                ], 403);
            }
        }

        // Only the first user or Captain can create Captain.
        if ($requestedRole === 'Captain') {
            if (User::count() > 0 && (!$currentUser || !$currentUser->isSuperAdmin())) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Forbidden: only existing Captain can create another Captain.',
                ], 403);
            }
        }

        $user = User::create([
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'password' => Hash::make($request->input('password')),
            'role_id' => $role->id,
        ]);

        $token = $user->createToken('api-token')->plainTextToken;

        return response()->json([
            'status' => 'success',
            'data' => [
                'user' => $user,
                'token' => $token,
            ],
            'message' => 'Registration successful',
        ], 201);
    }

    public function login(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation error',
                'errors' => $validator->errors(),
            ], 422);
        }

        $user = User::where('email', $request->input('email'))->first();

        if (!$user || !Hash::check($request->input('password'), $user->password)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid credentials',
            ], 401);
        }

        $token = $user->createToken('api-token')->plainTextToken;

        return response()->json([
            'status' => 'success',
            'data' => [
                'user' => $user,
                'token' => $token,
            ],
            'message' => 'Login successful',
        ], 200);
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()?->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Logged out successfully',
        ], 200);
    }
}
