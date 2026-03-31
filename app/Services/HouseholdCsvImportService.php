<?php

namespace App\Services;

use App\Models\Household;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class HouseholdCsvImportService
{
    public function import(string $path): array
    {
        $file = fopen($path, 'r');
        if (!$file) {
            throw new \RuntimeException('Unable to open CSV file.');
        }

        $headers = fgetcsv($file);
        if (!$headers || count($headers) === 0) {
            fclose($file);
            throw new \RuntimeException('CSV must contain headers.');
        }

        $successCount = 0;
        $failed = [];
        $rowIndex = 1;

        DB::transaction(function () use ($file, $headers, &$rowIndex, &$successCount, &$failed) {
            while (($data = fgetcsv($file)) !== false) {
                $rowIndex++;
                $row = array_combine($headers, $data);

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

                try {
                    $household = Household::firstOrCreate(
                        ['household_id' => $row['household_id']],
                        [
                            'address' => $row['address'] ?? '',
                            'sitio' => $row['sitio'] ?? null,
                            'purok' => $row['sitio'] ?? 'Unknown',
                            'emergency_contact' => $row['emergency_contact'] ?? 'N/A',
                        ]
                    );

                    $household->members()->create([
                        'first_name' => $row['first_name'],
                        'middle_name' => $row['middle_name'] ?? null,
                        'last_name' => $row['last_name'],
                        'suffix' => $row['suffix'] ?? null,
                        'birth_date' => Carbon::parse($row['birth_date'])->toDateString(),
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
                        'age' => Carbon::parse($row['birth_date'])->age,
                    ]);
                    $successCount++;
                } catch (\Throwable $e) {
                    Log::error("Household CSV import failed row {$rowIndex}: {$e->getMessage()}");
                    $failed[] = ['row' => $rowIndex, 'error' => $e->getMessage()];
                }
            }
        });

        fclose($file);

        return [
            'success_count' => $successCount,
            'failed_rows' => count($failed),
            'errors' => $failed,
        ];
    }
}
