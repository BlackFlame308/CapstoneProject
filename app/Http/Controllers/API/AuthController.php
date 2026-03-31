<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Household;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function register(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'nullable|string|min:8|confirmed',
            'role' => 'required|string|in:Captain,Encoder,Responder,Evacuation Officer,Household',
            'household' => 'sometimes|array',
            'household.household_id' => 'sometimes|string|max:255',
            'household.address' => 'sometimes|required_with:role|nullable|string|max:255',
            'household.sitio' => 'sometimes|nullable|string|max:255',
            'household.purok' => 'sometimes|nullable|string|max:255',
            'household.emergency_contact' => 'sometimes|nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation error',
                'errors' => $validator->errors(),
            ], 422);
        }

        $requestedRole = $request->input('role');
        $role = Role::firstWhere('name', $requestedRole);

        if (!$role) {
            return response()->json([
                'status' => 'error',
                'message' => 'Role not found',
            ], 404);
        }

        $currentUser = auth()->user();

        // If this is the very first account, allow creating Captain to bootstrap.
        if (User::count() === 0) {
            if ($requestedRole !== 'Captain') {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Forbidden: first account must be Captain.',
                ], 403);
            }
        } else {
            if (!$currentUser || !$currentUser->isSuperAdmin()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Forbidden: only Captain can create accounts.',
                ], 403);
            }

            // Prevent creating Super Admin legacy via API.
            if ($requestedRole === 'Super Admin') {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Forbidden: cannot directly assign Super Admin role.',
                ], 403);
            }
        }

        $generatedPassword = null;
        $rawPassword = $request->input('password');

        if (!$rawPassword) {
            $generatedPassword = Str::random(12);
            $rawPassword = $generatedPassword;
        }

        $householdId = null;
        if ($requestedRole === 'Household') {
            $householdData = $request->input('household', []);
            if (empty($householdData['address'])) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Household details are required for Household accounts.',
                ], 422);
            }

            $household = Household::create([
                'household_id' => $householdData['household_id'] ?? null,
                'address' => $householdData['address'],
                'sitio' => $householdData['sitio'] ?? null,
                'purok' => $householdData['purok'] ?? null,
                'emergency_contact' => $householdData['emergency_contact'] ?? null,
            ]);

            $householdId = $household->id;
        }

        $user = User::create([
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'password' => Hash::make($rawPassword),
            'role_id' => $role->id,
            'household_id' => $householdId,
        ]);

        $token = $user->createToken('api-token')->plainTextToken;

        $response = [
            'status' => 'success',
            'data' => [
                'user' => $user,
                'token' => $token,
            ],
            'message' => 'Registration successful',
        ];

        if ($generatedPassword) {
            $response['data']['temporary_password'] = $generatedPassword;
        }

        return response()->json($response, 201);
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

    public function changePassword(Request $request): JsonResponse
    {
        $request->validate([
            'current_password' => 'required|string',
            'new_password' => 'required|string|min:8|confirmed',
        ]);

        $user = $request->user();

        if (!Hash::check($request->input('current_password'), $user->password)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Current password does not match.',
            ], 403);
        }

        $user->password = Hash::make($request->input('new_password'));
        $user->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Password changed successfully.',
        ], 200);
    }
}
