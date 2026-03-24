<?php

namespace App\Http\Controllers;

use App\Models\Analytic;
use Illuminate\Http\Request;

class AnalyticController extends Controller
{
    public function index()
    {
        $barangayAnalytics = Analytic::where('type', 'barangay')->first();
        $sitioAnalytics = Analytic::where('type', 'sitio')->get();
        return view('analytics.index', compact('barangayAnalytics', 'sitioAnalytics'));
    }
}