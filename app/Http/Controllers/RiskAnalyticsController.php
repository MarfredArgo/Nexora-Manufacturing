<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class RiskAnalyticsController extends Controller
{
    /**
     * Display the blank/empty risk analytics console view.
     */
    public function index(Request $request)
    {
        // 1. Capture the timeframe filter option (para gumana pa rin ang UI toggles)
        $timeframe = $request->input('timeframe', '30_days');
        
        // 2. Lahat ng metrics ay zeroed out dahil walang data
        $totalRisks = 0;
        $mitigationIndex = 0.0;
        $avgResolutionFormatted = 'N/A';

        $totalHazards = 0;
        $controlledHazards = 0;
        $unassignedRisks = 0; 

        // 3. Empty status distributions counts
        $statusDistribution = [
            'unmitigated' => 0,
            'in_progress' => 0,
            'secured'     => 0,
        ];

        // 4. Walang makikitang department vulnerability vectors
        $vulnerabilityVectors = [];

        // Magpasa tayo ng flag para alam ng frontend na blangko ang console
        $hasData = false;

        return view('analytics', compact(
            'timeframe',
            'totalRisks',
            'mitigationIndex',
            'avgResolutionFormatted',
            'controlledHazards',
            'totalHazards',
            'unassignedRisks',
            'statusDistribution',
            'vulnerabilityVectors',
            'hasData'
        ));
    }

    /**
     * CSV/XLS Download Stream for an empty system state
     */
    public function export(Request $request)
    {
        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=nexora_empty_report.csv",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $callback = function() {
            $file = fopen('php://output', 'w');
            // Maglalabas lang ng header columns pero walang records sa ilalim
            fputcsv($file, ['Risk ID', 'Title', 'Department', 'Status', 'Resolution Hours']);
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}