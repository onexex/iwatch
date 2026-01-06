<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\Incident;
use App\Models\IncidentWatermark;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class IncidentController extends Controller
{
//    public function index()
//     {
//         return inertia('MapView', [
//             'incidents' => Incident::with('barangay')->get()
//         ]);
//     }

     public function index()
    {
        // We use 'with' to eager-load the barangay coordinates 
        // linked via address_id
        return response()->json(Incident::with('barangay','attachments')->get());
    }

    public function processedMessages()
    {
        $incidents = Incident::orderBy('created_at', 'desc')
            ->with('barangay', 'classification', 'classification', 'attachments')
            ->paginate(10)
            ->withQueryString();
        
        $watermarks = IncidentWatermark::get();

        return Inertia::render('Messages/ProcessedMessage', [
            'messages' => $incidents,
            'watermarks' => $watermarks,
        ]);
    }

    public function download(Incident $incident, Request $request)
    {
        $watermark = IncidentWatermark::where('id', $request->copyFor)->first();
        $pdf = Pdf::loadView('incidents.incidentpdf', [
            'incident' => $incident,
            'copyFor' => $watermark->name ?? '',
            'bgColor' => $watermark->color ?? '#c7080826',
        ]);

        return $pdf->stream("incident-{$incident->id}.pdf");
    }
}