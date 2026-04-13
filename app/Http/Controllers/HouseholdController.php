<?php

namespace App\Http\Controllers;

use App\Models\Household;
use App\Models\Member;
use App\Services\HouseholdCsvImportService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class HouseholdController extends Controller
{
    public function index()
    {
        if (! $this->userCanAccessHouseholdRegistry(auth()->user())) {
            abort(403, 'Unauthorized to view households.');
        }

        $households = Household::with('members')->paginate(10);
        return view('households.index', compact('households'));
    }

    public function create()
    {
        // Check if user can add households
        if (!auth()->user()->hasPermission('add_households') && !auth()->user()->isSuperAdmin()) {
            abort(403, 'Unauthorized to create households.');
        }
        return view('households.create');
    }

    public function store(Request $request)
    {
        if (!auth()->user()->hasPermission('add_households') && !auth()->user()->isSuperAdmin()) {
            abort(403, 'Unauthorized to create households.');
        }

        $request->validate([
            'household_id' => 'nullable|unique:households',
            'address' => 'required',
            'sitio' => 'nullable|string|max:255',
            'purok' => 'nullable|string|max:255',
            'region' => 'nullable|string|max:255',
            'province' => 'nullable|string|max:255',
            'city_mun' => 'nullable|string|max:255',
            'barangay' => 'nullable|string|max:255',
            'household_number' => 'nullable|string|max:255',
            'headname' => 'required|string|max:255',
            'contact_number' => 'required|string|max:255',
            'emergency_contact' => 'required|string|max:255',
            'members' => 'nullable|array',
            'members.*.name' => 'required_with:members|string|max:255',
            'members.*.age' => 'required_with:members|integer|min:0',
            'members.*.gender' => 'required_with:members|in:male,female,other',
            'members.*.special_needs' => 'nullable|in:child,adult,senior,pwd',
            'members.*.is_pwd' => 'nullable|boolean',
        ]);

        $household = Household::create([
            'household_id' => $request->input('household_id') ?: $this->generateHouseholdId(),
            'address' => $request->input('address'),
            'sitio' => $request->input('sitio'),
            'purok' => $request->input('purok'),
            'region' => $request->input('region'),
            'province' => $request->input('province'),
            'city_mun' => $request->input('city_mun'),
            'barangay' => $request->input('barangay'),
            'household_number' => $request->input('household_number'),
            'headname' => $request->input('headname'),
            'contact_number' => $request->input('contact_number'),
            'emergency_contact' => $request->input('emergency_contact'),
        ]);

        if ($request->filled('members')) {
            foreach ($request->members as $memberData) {
                $memberData['is_pwd'] = boolval($memberData['is_pwd'] ?? false);
                if (empty($memberData['special_needs'])) {
                    if ($memberData['is_pwd']) {
                        $memberData['special_needs'] = 'pwd';
                    } elseif ($memberData['age'] >= 60) {
                        $memberData['special_needs'] = 'senior';
                    } elseif ($memberData['age'] < 18) {
                        $memberData['special_needs'] = 'child';
                    } else {
                        $memberData['special_needs'] = 'adult';
                    }
                }
                $household->members()->create($memberData);
            }
        }

        $this->updateAnalytics();

        return redirect()->route('households.index')->with('success', 'Household created successfully.');
    }

    public function show(Household $household)
    {
        if (! $this->userCanAccessHouseholdRegistry(auth()->user())) {
            abort(403, 'Unauthorized to view households.');
        }

        $household->load('members');
        return view('households.show', compact('household'));
    }

    public function edit(Household $household)
    {
        // Check if user can update households
        if (!auth()->user()->hasPermission('update_households') && !auth()->user()->isSuperAdmin()) {
            abort(403, 'Unauthorized to update households.');
        }
        $household->load('members');
        return view('households.edit', compact('household'));
    }

    public function update(Request $request, Household $household)
    {
        if (!auth()->user()->hasPermission('update_households') && !auth()->user()->isSuperAdmin()) {
            abort(403, 'Unauthorized to update households.');
        }

        $request->validate([
            'household_id' => 'required|unique:households,household_id,' . $household->id,
            'address' => 'required',
            'sitio' => 'nullable|string|max:255',
            'purok' => 'nullable|string|max:255',
            'region' => 'nullable|string|max:255',
            'province' => 'nullable|string|max:255',
            'city_mun' => 'nullable|string|max:255',
            'barangay' => 'nullable|string|max:255',
            'household_number' => 'nullable|string|max:255',
            'headname' => 'required|string|max:255',
            'contact_number' => 'required|string|max:255',
            'emergency_contact' => 'required|string|max:255',
            'members' => 'nullable|array',
            'members.*.name' => 'required_with:members|string|max:255',
            'members.*.age' => 'required_with:members|integer|min:0',
            'members.*.gender' => 'required_with:members|in:male,female,other',
            'members.*.special_needs' => 'nullable|in:child,adult,senior,pwd',
            'members.*.is_pwd' => 'nullable|boolean',
        ]);

        $household->update([
            'household_id' => $request->input('household_id'),
            'address' => $request->input('address'),
            'sitio' => $request->input('sitio'),
            'purok' => $request->input('purok'),
            'region' => $request->input('region'),
            'province' => $request->input('province'),
            'city_mun' => $request->input('city_mun'),
            'barangay' => $request->input('barangay'),
            'household_number' => $request->input('household_number'),
            'headname' => $request->input('headname'),
            'contact_number' => $request->input('contact_number'),
            'emergency_contact' => $request->input('emergency_contact'),
        ]);

        $household->members()->delete();

        if ($request->filled('members')) {
            foreach ($request->members as $memberData) {
                $memberData['is_pwd'] = boolval($memberData['is_pwd'] ?? false);
                if (empty($memberData['special_needs'])) {
                    if ($memberData['is_pwd']) {
                        $memberData['special_needs'] = 'pwd';
                    } elseif ($memberData['age'] >= 60) {
                        $memberData['special_needs'] = 'senior';
                    } elseif ($memberData['age'] < 18) {
                        $memberData['special_needs'] = 'child';
                    } else {
                        $memberData['special_needs'] = 'adult';
                    }
                }
                $household->members()->create($memberData);
            }
        }

        $this->updateAnalytics();

        return redirect()->route('households.index')->with('success', 'Household updated successfully.');
    }

    public function destroy(Household $household)
    {
        // Encoders cannot delete households
        if (auth()->user()->hasRole('Encoder') && !auth()->user()->isSuperAdmin()) {
            return back()->with('error', 'You do not have permission to delete households.');
        }

        $household->delete();
        $this->updateAnalytics();
        return redirect()->route('households.index')->with('success', 'Household deleted successfully.');
    }

    public function uploadForm()
    {
        if (!auth()->user()->hasPermission('add_households') && !auth()->user()->isSuperAdmin()) {
            abort(403, 'Unauthorized to upload households.');
        }

        return view('households.upload');
    }

    public function upload(Request $request, HouseholdCsvImportService $importService)
    {
        if (!auth()->user()->hasPermission('add_households') && !auth()->user()->isSuperAdmin()) {
            abort(403, 'Unauthorized to upload households.');
        }

        $request->validate([
            'file' => 'required|file|mimes:csv,txt',
        ]);

        $result = $importService->import($request->file('file')->getRealPath());

        return redirect()->route('households.index')->with('success', 'Household CSV upload completed. ' . $result['success_count'] . ' rows imported.');
    }

    public function import(Request $request, HouseholdCsvImportService $importService)
    {
        if (!auth()->user()->hasPermission('add_households') && !auth()->user()->isSuperAdmin()) {
            abort(403, 'Unauthorized to import households.');
        }

        $request->validate([
            'file' => 'required|file|mimes:csv,txt',
        ]);

        $result = $importService->import($request->file('file')->getRealPath());

        return redirect()->route('households.index')->with('success', 'Household CSV import completed. ' . $result['success_count'] . ' rows imported.');
    }

    public function export()
    {
        if (! $this->userCanAccessHouseholdRegistry(auth()->user())) {
            abort(403, 'Unauthorized to export households.');
        }

        $households = Household::with('members')->get();

        $filename = 'households_export_' . now()->format('Y-m-d_H-i-s') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function () use ($households) {
            $file = fopen('php://output', 'w');

            // Write headers
            fputcsv($file, [
                'household_id', 'address', 'purok', 'region', 'province', 'city_mun', 'barangay', 'household_number',
                'headname', 'contact_number', 'emergency_contact',
                'philsys_card_no', 'last_name', 'suffix', 'first_name', 'middle_name', 'birth_date', 'birth_place',
                'sex', 'civil_status', 'religion', 'residence_address', 'citizenship', 'profession', 'education_level',
                'contact_number_member', 'email', 'is_graduate', 'is_pwd', 'date_accomplished', 'name_signature',
                'attested_by', 'left_thumbmark', 'right_thumbmark', 'age'
            ]);

            foreach ($households as $household) {
                foreach ($household->members as $member) {
                    fputcsv($file, [
                        $household->household_id,
                        $household->address,
                        $household->purok,
                        $household->region,
                        $household->province,
                        $household->city_mun,
                        $household->barangay,
                        $household->household_number,
                        $household->headname,
                        $household->contact_number,
                        $household->emergency_contact,
                        $member->philips_card_no,
                        $member->last_name,
                        $member->suffix,
                        $member->first_name,
                        $member->middle_name,
                        $member->birth_date ? $member->birth_date->format('m/d/Y') : '',
                        $member->birth_place,
                        $member->sex,
                        $member->civil_status,
                        $member->religion,
                        $member->residence_address,
                        $member->citizenship,
                        $member->profession,
                        $member->education_level,
                        $member->contact_number,
                        $member->email,
                        $member->is_graduate ? 'true' : 'false',
                        $member->is_pwd ? 'true' : 'false',
                        $member->date_accomplished,
                        $member->name_signature,
                        $member->attested_by,
                        $member->left_thumbmark ? 'true' : 'false',
                        $member->right_thumbmark ? 'true' : 'false',
                        $member->age,
                    ]);
                }
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    private function updateAnalytics()
    {
        // Compute barangay analytics
        $totalHouseholds = Household::count();
        $totalPopulation = Member::count();
        $totalSeniors = Member::where('age', '>=', 60)->count();
        $totalPwd = Member::where('is_pwd', true)->count();
        $totalChildren = Member::where('age', '<', 18)->count();
        $totalAdults = Member::whereBetween('age', [18, 59])->count();

        \App\Models\Analytic::updateOrCreate(
            ['type' => 'barangay', 'sitio' => null],
            [
                'total_households' => $totalHouseholds,
                'total_population' => $totalPopulation,
                'total_seniors' => $totalSeniors,
                'total_pwd' => $totalPwd,
                'total_children' => $totalChildren,
                'total_adults' => $totalAdults,
            ]
        );

        // Compute sitio analytics
        $sitios = Household::select('sitio')->distinct()->pluck('sitio');
        foreach ($sitios as $sitio) {
            $households = Household::where('sitio', $sitio)->with('members')->get();
            $totalHouseholds = $households->count();
            $members = $households->pluck('members')->flatten();
            $totalPopulation = $members->count();
            $totalSeniors = $members->where('age', '>=', 60)->count();
            $totalPwd = $members->where('special_needs', 'pwd')->count();
            $totalChildren = $members->where('age', '<', 18)->count();
            $totalAdults = $members->where('age', '>=', 18)->where('age', '<=', 59)->count();

            \App\Models\Analytic::updateOrCreate(
                ['type' => 'sitio', 'sitio' => $sitio],
                [
                    'total_households' => $totalHouseholds,
                    'total_population' => $totalPopulation,
                    'total_seniors' => $totalSeniors,
                    'total_pwd' => $totalPwd,
                    'total_children' => $totalChildren,
                    'total_adults' => $totalAdults,
                ]
            );
        }
    }

    private function generateHouseholdId(): string
    {
        do {
            $id = 'HH-' . strtoupper(Str::random(8));
        } while (Household::where('household_id', $id)->exists());

        return $id;
    }

    /**
     * Who may open the household registry list or view a household record.
     */
    private function userCanAccessHouseholdRegistry($user): bool
    {
        return $user->hasPermission('view_households')
            || $user->hasPermission('add_households')
            || $user->hasPermission('update_households')
            || $user->isSuperAdmin();
    }
}
