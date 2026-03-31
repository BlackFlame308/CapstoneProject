<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreMemberRequest;
use App\Http\Requests\UpdateMemberRequest;
use App\Models\Member;
use App\Models\Household;
use Illuminate\Http\JsonResponse;

class MemberController extends Controller
{
    public function index(): JsonResponse
    {
        $members = Member::with('household')->paginate(25);

        return response()->json([
            'status' => 'success',
            'data' => $members,
            'message' => 'Members retrieved',
        ], 200);
    }

    public function store(StoreMemberRequest $request): JsonResponse
    {
        $household = Household::findOrFail($request->input('household_id'));

        $member = $household->members()->create([
            'first_name' => $request->input('first_name'),
            'middle_name' => $request->input('middle_name'),
            'last_name' => $request->input('last_name'),
            'suffix' => $request->input('suffix'),
            'birth_date' => $request->input('birth_date'),
            'birth_place' => $request->input('birth_place'),
            'sex' => $request->input('sex'),
            'civil_status' => $request->input('civil_status'),
            'religion' => $request->input('religion'),
            'citizenship' => $request->input('citizenship'),
            'profession' => $request->input('profession'),
            'contact_number' => $request->input('contact_number'),
            'email' => $request->input('email'),
            'education_level' => $request->input('education_level'),
            'is_graduate' => $request->boolean('is_graduate'),
            'is_pwd' => $request->boolean('is_pwd'),
            'age' => now()->diffInYears(\Carbon\Carbon::parse($request->input('birth_date'))),
        ]);

        return response()->json([
            'status' => 'success',
            'data' => $member,
            'message' => 'Member added',
        ], 201);
    }

    public function show(Member $member): JsonResponse
    {
        $member->load('household');

        return response()->json([
            'status' => 'success',
            'data' => $member,
            'message' => 'Member retrieved',
        ], 200);
    }

    public function update(UpdateMemberRequest $request, Member $member): JsonResponse
    {
        $member->update([
            'household_id' => $request->input('household_id'),
            'first_name' => $request->input('first_name'),
            'middle_name' => $request->input('middle_name'),
            'last_name' => $request->input('last_name'),
            'suffix' => $request->input('suffix'),
            'birth_date' => $request->input('birth_date'),
            'birth_place' => $request->input('birth_place'),
            'sex' => $request->input('sex'),
            'civil_status' => $request->input('civil_status'),
            'religion' => $request->input('religion'),
            'citizenship' => $request->input('citizenship'),
            'profession' => $request->input('profession'),
            'contact_number' => $request->input('contact_number'),
            'email' => $request->input('email'),
            'education_level' => $request->input('education_level'),
            'is_graduate' => $request->boolean('is_graduate'),
            'is_pwd' => $request->boolean('is_pwd'),
            'age' => now()->diffInYears(\Carbon\Carbon::parse($request->input('birth_date'))),
        ]);

        return response()->json([
            'status' => 'success',
            'data' => $member,
            'message' => 'Member updated',
        ], 200);
    }

    public function destroy(Member $member): JsonResponse
    {
        $member->delete();

        return response()->json([
            'status' => 'success',
            'data' => null,
            'message' => 'Member deleted',
        ], 200);
    }
}
