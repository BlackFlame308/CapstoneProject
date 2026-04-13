<?php

namespace App\Services;

use App\Models\Household;
use App\Models\Member;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

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
        $householdsProcessed = [];

        DB::transaction(function () use ($file, $headers, &$rowIndex, &$successCount, &$failed, &$householdsProcessed) {
            while (($data = fgetcsv($file)) !== false) {
                $rowIndex++;
                
                // Skip empty rows
                if (empty(array_filter($data))) {
                    continue;
                }

                $row = array_combine($headers, $data);

                // Validate expanded CSV format with members
                $validator = Validator::make($row, [
                    'household_id' => 'required|string|max:255',
                    'address' => 'required|string|max:255',
                    'purok' => 'required|string|max:255',
                    'region' => 'required|string|max:255',
                    'province' => 'required|string|max:255',
                    'city_mun' => 'required|string|max:255',
                    'barangay' => 'required|string|max:255',
                    'household_number' => 'required|string|max:255',
                    'headname' => 'required|string|max:255',
                    'contact_number' => 'required|string|max:255',
                    'emergency_contact' => 'required|string|max:255',
                    'philsys_card_no' => 'nullable|string|max:255',
                    'last_name' => 'required|string|max:255',
                    'suffix' => 'nullable|string|max:255',
                    'first_name' => 'required|string|max:255',
                    'middle_name' => 'nullable|string|max:255',
                    'birth_date' => 'required|date_format:m/d/Y',
                    'birth_place' => 'nullable|string|max:255',
                    'sex' => 'required|in:male,female,other',
                    'civil_status' => 'nullable|string|max:255',
                    'religion' => 'nullable|string|max:255',
                    'residence_address' => 'nullable|string|max:255',
                    'citizenship' => 'nullable|string|max:255',
                    'profession' => 'nullable|string|max:255',
                    'education_level' => 'nullable|string|max:255',
                    'contact_number_member' => 'nullable|string|max:255',
                    'email' => 'nullable|email|max:255',
                    'is_graduate' => 'nullable|boolean',
                    'is_pwd' => 'nullable|boolean',
                    'date_accomplished' => 'nullable|string|max:255',
                    'name_signature' => 'nullable|string|max:255',
                    'attested_by' => 'nullable|string|max:255',
                    'left_thumbmark' => 'nullable|boolean',
                    'right_thumbmark' => 'nullable|boolean',
                    'age' => 'required|integer|min:0|max:150',
                ]);

                if ($validator->fails()) {
                    $failed[] = ['row' => $rowIndex, 'errors' => $validator->errors()->all()];
                    continue;
                }

                try {
                    // Create or update household (only once per household_id)
                    if (!isset($householdsProcessed[$row['household_id']])) {
                        $household = Household::firstOrCreate(
                            ['household_id' => $row['household_id']],
                            [
                                'address' => $row['address'],
                                'purok' => $row['purok'],
                                'region' => $row['region'],
                                'province' => $row['province'],
                                'city_mun' => $row['city_mun'],
                                'barangay' => $row['barangay'],
                                'household_number' => $row['household_number'],
                                'headname' => $row['headname'],
                                'contact_number' => $row['contact_number'],
                                'emergency_contact' => $row['emergency_contact'],
                            ]
                        );
                        $householdsProcessed[$row['household_id']] = $household->id;
                    } else {
                        $household = Household::find($householdsProcessed[$row['household_id']]);
                    }

                    // Create member record using detailed RBI fields
                    Member::create([
                        'household_id' => $household->id,
                        'philips_card_no' => $row['philsys_card_no'] ?? null,
                        'last_name' => $row['last_name'],
                        'suffix' => $row['suffix'] ?? null,
                        'first_name' => $row['first_name'],
                        'middle_name' => $row['middle_name'] ?? null,
                        'birth_date' => \Carbon\Carbon::createFromFormat('m/d/Y', $row['birth_date']),
                        'birth_place' => $row['birth_place'] ?? null,
                        'sex' => $row['sex'],
                        'civil_status' => $row['civil_status'] ?? null,
                        'religion' => $row['religion'] ?? null,
                        'residence_address' => $row['residence_address'] ?? null,
                        'citizenship' => $row['citizenship'] ?? null,
                        'profession' => $row['profession'] ?? null,
                        'education_level' => $row['education_level'] ?? null,
                        'is_graduate' => filter_var($row['is_graduate'] ?? false, FILTER_VALIDATE_BOOLEAN),
                        'is_pwd' => filter_var($row['is_pwd'] ?? false, FILTER_VALIDATE_BOOLEAN),
                        'contact_number' => $row['contact_number_member'] ?? null,
                        'email' => $row['email'] ?? null,
                        'date_accomplished' => $row['date_accomplished'] ?? null,
                        'name_signature' => $row['name_signature'] ?? null,
                        'attested_by' => $row['attested_by'] ?? null,
                        'left_thumbmark' => filter_var($row['left_thumbmark'] ?? false, FILTER_VALIDATE_BOOLEAN),
                        'right_thumbmark' => filter_var($row['right_thumbmark'] ?? false, FILTER_VALIDATE_BOOLEAN),
                        'age' => $row['age'],
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
