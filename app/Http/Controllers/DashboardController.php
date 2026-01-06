<?php

// app/Http/Controllers/DashboardController.php

namespace App\Http\Controllers;

use App\Models\Incident;
use App\Models\SmsMessage;
use App\Models\Barangay;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
{
    // 1. Calculate Trend Percentage (Current Month vs Last Month)
    $currentMonthCount = Incident::whereMonth('created_at', now()->month)
        ->whereYear('created_at', now()->year)
        ->count();

    $lastMonthCount = Incident::whereMonth('created_at', now()->subMonth()->month)
        ->whereYear('created_at', now()->subMonth()->year)
        ->count();

    // Calculate percentage change
    $trendValue = 0;
    if ($lastMonthCount > 0) {
        $trendValue = (($currentMonthCount - $lastMonthCount) / $lastMonthCount) * 100;
    } elseif ($currentMonthCount > 0) {
        $trendValue = 100; // 100% increase if there was nothing last month
    }

    // 2. KPI Counts
    $stats = [
        'new_sms' => SmsMessage::count(),
        'total_incidents' => Incident::count(),
        'pending_analysis' => SmsMessage::where('is_read', 0)->count(),
        'processed_today' => Incident::whereDate('created_at', today())->count(),
        'trend_value' => round($trendValue, 1), // Pass the trend here
    ];

    // 3. Data for Barangay Chart
    $byBarangay = Incident::join('barangays', 'incidents.address_id', '=', 'barangays.id')
        ->select('barangays.barangay as name', DB::raw('count(*) as total'))
        ->groupBy('barangays.barangay')
        ->pluck('total', 'name');

    // 4. Data for Classification Chart
    $byType = Incident::join('classifications', 'incidents.classification_id', '=', 'classifications.id')
        ->select('classifications.name', DB::raw('count(*) as total'))
        ->groupBy('classifications.name')
        ->pluck('total', 'name');

    // 5. NEW: Data for Manner Acquired (Source)
    $byManner = Incident::select('manner_acquired', DB::raw('count(*) as total'))
        ->whereNotNull('manner_acquired')
        ->groupBy('manner_acquired')
        ->pluck('total', 'manner_acquired');

    // 6. Recent activity
    $recent = Incident::with(['barangay', 'classification'])
        ->latest()
        ->limit(6)
        ->get();

    return Inertia::render('Dashboard', [
        'stats' => $stats,
        'chartData' => [
            'barangay' => $byBarangay,
            'types' => $byType,
            'manner' => $byManner // Pass the new manner data
        ],
        'recent' => $recent
    ]);
}
}