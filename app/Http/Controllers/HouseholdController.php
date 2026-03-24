<?php

namespace App\Http\Controllers;

use App\Models\Household;
use App\Models\Member;
use Illuminate\Http\Request;

class HouseholdController extends Controller
{
    public function index()
    {
        $households = Household::with('members')->paginate(10);
        return view('households.index', compact('households'));
    }

    public function create()
    {
        return view('households.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'household_id' => 'required|unique:households',
            'address' => 'required',
            'purok' => 'required',
            'emergency_contact' => 'required',
            'members' => 'array',
            'members.*.name' => 'required',
            'members.*.age' => 'required|integer',
            'members.*.gender' => 'required|in:male,female,other',
            'members.*.special_needs' => 'nullable|in:child,adult,senior,pwd',
        ]);

        $household = Household::create($request->only(['household_id', 'address', 'purok', 'emergency_contact']));

        if ($request->members) {
            foreach ($request->members as $memberData) {
                $household->members()->create($memberData);
            }
        }

        // Update analytics
        $this->updateAnalytics();

        return redirect()->route('households.index')->with('success', 'Household created successfully.');
    }

    public function show(Household $household)
    {
        $household->load('members');
        return view('households.show', compact('household'));
    }

    public function edit(Household $household)
    {
        $household->load('members');
        return view('households.edit', compact('household'));
    }

    public function update(Request $request, Household $household)
    {
        $request->validate([
            'household_id' => 'required|unique:households,household_id,' . $household->id,
            'address' => 'required',
            'purok' => 'required',
            'emergency_contact' => 'required',
            'members' => 'array',
            'members.*.name' => 'required',
            'members.*.age' => 'required|integer',
            'members.*.gender' => 'required|in:male,female,other',
            'members.*.special_needs' => 'nullable|in:child,adult,senior,pwd',
        ]);

        $household->update($request->only(['household_id', 'address', 'purok', 'emergency_contact']));

        $household->members()->delete(); // Delete existing members

        if ($request->members) {
            foreach ($request->members as $memberData) {
                $household->members()->create($memberData);
            }
        }

        $this->updateAnalytics();

        return redirect()->route('households.index')->with('success', 'Household updated successfully.');
    }

    public function destroy(Household $household)
    {
        $household->delete();
        $this->updateAnalytics();
        return redirect()->route('households.index')->with('success', 'Household deleted successfully.');
    }

    private function updateAnalytics()
    {
        // Compute barangay analytics
        $totalHouseholds = Household::count();
        $totalPopulation = Member::count();
        $totalSeniors = Member::where('age', '>=', 60)->count();
        $totalPwd = Member::where('special_needs', 'pwd')->count();
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
        $sitios = Household::select('purok')->distinct()->pluck('purok');
        foreach ($sitios as $sitio) {
            $households = Household::where('purok', $sitio)->with('members')->get();
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
}