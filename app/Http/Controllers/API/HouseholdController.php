<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreHouseholdRequest;
use App\Http\Requests\UpdateHouseholdRequest;
use App\Models\Household;
use App\Models\Member;
use App\Services\HouseholdCsvImportService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Carbon\Carbon;

class HouseholdController extends Controller
{
    public function index(): JsonResponse
    {
        $households = Household::with('members')->paginate(20);

        return response()->json([
            'status' => 'success',
            'data' => $households,
            'message' => 'Households retrieved',
        ], 200);
    }

    public function store(StoreHouseholdRequest $request): JsonResponse
    {
        $household = null;

        DB::transaction(function () use ($request, &$household) {
            $household = Household::create([
                'household_id' => $request->input('household_id', $this->generateHouseholdId()),
                'address' => $request->input('address'),
                'sitio' => $request->input('sitio'),
                'purok' => $request->input('purok'),
                'emergency_contact' => $request->input('emergency_contact'),
            ]);

            if ($request->filled('members')) {
                foreach ($request->input('members') as $memberData) {
                    $memberData['age'] = $this->computeAge($memberData['birth_date']);
                    $memberData['is_pwd'] = boolval($memberData['is_pwd'] ?? false);
                    $memberData['is_graduate'] = boolval($memberData['is_graduate'] ?? false);

                    $household->members()->create($memberData);
                }
            }
        });

        return response()->json([
            'status' => 'success',
            'data' => $household->load('members'),
            'message' => 'Household created',
        ], 201);
    }

    public function show(Household $household): JsonResponse
    {
        $household->load('members');

        return response()->json([
            'status' => 'success',
            'data' => $household,
            'message' => 'Household fetched',
        ], 200);
    }

    public function update(UpdateHouseholdRequest $request, Household $household): JsonResponse
    {
        DB::transaction(function () use ($request, $household) {
            $household->update($request->only(['address', 'sitio', 'purok', 'emergency_contact']));

            if ($request->filled('members')) {
                $household->members()->delete();
                foreach ($request->input('members') as $memberData) {
                    $memberData['age'] = $this->computeAge($memberData['birth_date']);
                    $memberData['is_pwd'] = boolval($memberData['is_pwd'] ?? false);
                    $memberData['is_graduate'] = boolval($memberData['is_graduate'] ?? false);
                    $household->members()->create($memberData);
                }
            }
        });

        return response()->json([
            'status' => 'success',
            'data' => $household->load('members'),
            'message' => 'Household updated',
        ], 200);
    }

    public function destroy(Household $household): JsonResponse
    {
        $household->delete();

        return response()->json([
            'status' => 'success',
            'data' => null,
            'message' => 'Household deleted',
        ], 200);
    }

    public function uploadCsv(Request $request, HouseholdCsvImportService $importService): JsonResponse
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,txt',
        ]);

        $path = $request->file('file')->getRealPath();

        $result = $importService->import($path);

        return response()->json([
            'status' => 'success',
            'data' => $result,
            'message' => 'CSV process completed',
        ], 200);
    }

    private function generateHouseholdId(): string
    {
        do {
            $id = 'HH-' . strtoupper(Str::random(8));
        } while (Household::where('household_id', $id)->exists());

        return $id;
    }

    private function computeAge(string $birthDate): int
    {
        return now()->diffInYears(\Carbon\Carbon::make($birthDate));
    }
}
