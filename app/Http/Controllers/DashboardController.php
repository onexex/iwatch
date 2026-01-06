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
        // 1. KPI Counts
        $stats = [
            'new_sms' => SmsMessage::where('is_read', 0)->count(),
            'total_incidents' => Incident::count(),
            'pending_analysis' => Incident::whereNull('analysis')->count(),
            'processed_today' => Incident::whereDate('created_at', today())->count(),
        ];

        // 2. Data for Barangay Chart (Using your Barangay model fields)
        $byBarangay = Incident::join('barangays', 'incidents.address_id', '=', 'barangays.id')
            ->select('barangays.barangay as name', DB::raw('count(*) as total'))
            ->groupBy('barangays.barangay')
            ->pluck('total', 'name');

        // 3. Data for Classification Chart
        $byType = Incident::join('classifications', 'incidents.classification_id', '=', 'classifications.id')
            ->select('classifications.name', DB::raw('count(*) as total'))
            ->groupBy('classifications.name')
            ->pluck('total', 'name');

        // 4. Recent activity for the feed
        $recent = Incident::with(['barangay', 'classification'])
            ->latest()
            ->limit(6)
            ->get();

        return Inertia::render('Dashboard', [
            'stats' => $stats,
            'chartData' => [
                'barangay' => $byBarangay,
                'types' => $byType
            ],
            'recent' => $recent
        ]);
    }
}