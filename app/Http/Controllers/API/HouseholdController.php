<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreHouseholdRequest;
use App\Http\Requests\UpdateHouseholdRequest;
use App\Models\Household;
use App\Models\Member;
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

    public function uploadCsv(Request $request): JsonResponse
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,txt',
        ]);

        $path = $request->file('file')->getRealPath();
        $file = fopen($path, 'r');

        $headers = fgetcsv($file);
        $successCount = 0;
        $failed = [];

        DB::transaction(function () use ($file, $headers, &$successCount, &$failed) {
            $rowIndex = 1;
            while (($data = fgetcsv($file)) !== false) {
                $rowIndex++;
                $row = array_combine($headers, $data);

                try {
                    $validator = Validator::make($row, [
                        'household_id' => 'required|string',
                        'last_name' => 'required|string',
                        'first_name' => 'required|string',
                        'middle_name' => 'nullable|string',
                        'suffix' => 'nullable|string',
                        'birth_date' => 'required|date',
                        'birth_place' => 'nullable|string',
                        'sex' => 'required|in:male,female,other',
                        'civil_status' => 'nullable|string',
                        'religion' => 'nullable|string',
                        'address' => 'nullable|string',
                        'sitio' => 'nullable|string',
                        'citizenship' => 'nullable|string',
                        'profession' => 'nullable|string',
                        'contact_number' => 'nullable|string',
                        'email' => 'nullable|email',
                        'education_level' => 'nullable|string',
                        'is_graduate' => 'nullable|in:0,1,true,false',
                        'is_pwd' => 'nullable|in:0,1,true,false',
                    ]);

                    if ($validator->fails()) {
                        $failed[] = ['row' => $rowIndex, 'errors' => $validator->errors()->all()];
                        continue;
                    }

                    $household = Household::firstOrCreate(
                        ['household_id' => $row['household_id']],
                        [
                            'address' => $row['address'] ?? '',
                            'sitio' => $row['sitio'] ?? null,
                            'purok' => $row['sitio'] ?? 'Unknown',
                            'emergency_contact' => 'N/A',
                        ]
                    );

                    $memberData = [
                        'household_id' => $household->id,
                        'first_name' => $row['first_name'],
                        'middle_name' => $row['middle_name'] ?? null,
                        'last_name' => $row['last_name'],
                        'suffix' => $row['suffix'] ?? null,
                        'birth_date' => $row['birth_date'],
                        'birth_place' => $row['birth_place'] ?? null,
                        'sex' => $row['sex'],
                        'civil_status' => $row['civil_status'] ?? null,
                        'religion' => $row['religion'] ?? null,
                        'citizenship' => $row['citizenship'] ?? null,
                        'profession' => $row['profession'] ?? null,
                        'contact_number' => $row['contact_number'] ?? null,
                        'email' => $row['email'] ?? null,
                        'education_level' => $row['education_level'] ?? null,
                        'is_graduate' => filter_var($row['is_graduate'] ?? false, FILTER_VALIDATE_BOOLEAN),
                        'is_pwd' => filter_var($row['is_pwd'] ?? false, FILTER_VALIDATE_BOOLEAN),
                        'age' => $this->computeAge($row['birth_date']),
                    ];

                    $household->members()->create($memberData);
                    $successCount++;
                } catch (\Exception $e) {
                    $failed[] = ['row' => $rowIndex, 'error' => $e->getMessage()];
                }
            }
        });

        if (is_resource($file)) {
            fclose($file);
        }

        return response()->json([
            'status' => 'success',
            'data' => [
                'success_count' => $successCount,
                'failed_rows' => count($failed),
                'errors' => $failed,
            ],
            'message' => 'CSV process completed',
        ], 200);
    }

                try {
                    $validator = \Illuminate\Support\Facades\Validator::make($row, [
                        'household_id' => 'required|string',
                        'last_name' => 'required|string',
                        'first_name' => 'required|string',
                        'middle_name' => 'nullable|string',
                        'suffix' => 'nullable|string',
                        'birth_date' => 'required|date',
                        'birth_place' => 'nullable|string',
                        'sex' => 'required|in:male,female,other',
                        'civil_status' => 'nullable|string',
                        'religion' => 'nullable|string',
                        'address' => 'nullable|string',
                        'sitio' => 'nullable|string',
                        'citizenship' => 'nullable|string',
                        'profession' => 'nullable|string',
                        'contact_number' => 'nullable|string',
                        'email' => 'nullable|email',
                        'education_level' => 'nullable|string',
                        'is_graduate' => 'nullable|in:0,1,true,false',
                        'is_pwd' => 'nullable|in:0,1,true,false',
                    ]);

                    if ($validator->fails()) {
                        $failed[] = ['row' => $index + 1, 'errors' => $validator->errors()->all()];
                        continue;
                    }

                    $household = Household::firstOrCreate(
                        ['household_id' => $row['household_id']],
                        [
                            'address' => $row['address'] ?? '',
                            'sitio' => $row['sitio'] ?? null,
                            'purok' => $row['sitio'] ?? 'Unknown',
                            'emergency_contact' => 'N/A',
                        ]
                    );

                    $memberData = [
                        'household_id' => $household->id,
                        'first_name' => $row['first_name'],
                        'middle_name' => $row['middle_name'] ?? null,
                        'last_name' => $row['last_name'],
                        'suffix' => $row['suffix'] ?? null,
                        'birth_date' => $row['birth_date'],
                        'birth_place' => $row['birth_place'] ?? null,
                        'sex' => $row['sex'],
                        'civil_status' => $row['civil_status'] ?? null,
                        'religion' => $row['religion'] ?? null,
                        'citizenship' => $row['citizenship'] ?? null,
                        'profession' => $row['profession'] ?? null,
                        'contact_number' => $row['contact_number'] ?? null,
                        'email' => $row['email'] ?? null,
                        'education_level' => $row['education_level'] ?? null,
                        'is_graduate' => filter_var($row['is_graduate'] ?? false, FILTER_VALIDATE_BOOLEAN),
                        'is_pwd' => filter_var($row['is_pwd'] ?? false, FILTER_VALIDATE_BOOLEAN),
                        'age' => $this->computeAge($row['birth_date']),
                    ];

                    $household->members()->create($memberData);
                    $successCount++;
                } catch (\Exception $e) {
                    $failed[] = ['row' => $index + 1, 'error' => $e->getMessage()];
                }
            }
        });

        return response()->json([
            'status' => 'success',
            'data' => [
                'success_count' => $successCount,
                'failed_rows' => count($failed),
                'errors' => $failed,
            ],
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
