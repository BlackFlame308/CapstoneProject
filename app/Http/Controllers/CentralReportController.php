<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Report;
use App\Models\ApiToken;
use Illuminate\Support\Facades\Validator;

class CentralReportController extends Controller
{
    /**
     * Store reports coming from subsystems
     * Only allows requests with a valid API token
     */
    public function store(Request $request)
{
    //STEP 1: TOKEN VALIDATION
    $token = str_replace('Bearer ', '', $request->header('Authorization'));

    $validToken = ApiToken::where('token_value', $token)->first();

    if (!$validToken) {
        return response()->json([
            'message' => 'Unauthorized - Invalid Token'
        ], 401);
    }

    //STEP 2: INPUT VALIDATION
    $request->validate([
        'report_type' => 'required|string|max:255',
        'content_data' => 'required|array'
    ]);

    //STEP 3: STORE REPORT
    $report = Report::create([
        'source_module' => $validToken->module_name,
        'report_type' => $request->report_type,
        'content_data' => $request->content_data,
        'date_timestamp' => now()
    ]);

    // STEP 4: RESPONSE
    return response()->json([
        'message' => 'Report received successfully',
        'source' => $validToken->module_name,
        'type' => $request->report_type
    ], 201);
}

    /**
     * Get all reports
     */
    public function index()
    {
        $reports = Report::latest()->get();

        return response()->json($reports);
    }

    /**
     * View single report
     */
    public function show($id)
    {
        $report = Report::find($id);

        if (!$report) {
            return response()->json([
                'message' => 'Report not found'
            ], 404);
        }

        return response()->json($report);
    }

    /**
     * Validate an API token and return its module
     */
    public function validateToken(Request $request)
    {
        $tokenValue = $request->header('x-api-token') ?? $request->input('token');

        $token = ApiToken::where('token_value', $tokenValue)->first();

        if ($token) {
            return response()->json([
                'status' => 'success',
                'module' => $token->module_name
            ]);
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid token'
            ], 401);
        }
    }

    /**
     * List all API tokens (optional)
     */
    public function listTokens()
    {
        $tokens = ApiToken::all();

        return response()->json($tokens);
    }
}
   