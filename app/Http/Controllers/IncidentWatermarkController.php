<?php

namespace App\Http\Controllers;

use App\Models\IncidentWatermark;
use Illuminate\Http\Request;
use Inertia\Inertia;

class IncidentWatermarkController extends Controller
{

    public function index()
    {
        $incidentwatermarks = IncidentWatermark::get();

        return Inertia::render('IncidentWatermark/Index', [
            'incidentWatermarks' => $incidentwatermarks
        ]);
    }

    public function store(Request $request)
    {
        dd($request->all());

        IncidentWatermark::create([
            
        ]);
    }
}
