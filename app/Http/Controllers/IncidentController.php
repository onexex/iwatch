<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\Incident;
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

        return Inertia::render('Messages/ProcessedMessage', [
            'messages' => $incidents,
        ]);
    }

    public function download(Incident $incident, Request $request)
    {
        $pdf = Pdf::loadView('incidents.incidentpdf', [
            'incident' => $incident,
            'copyFor' => $request->copyFor
        ]);

        return $pdf->stream("incident-{$incident->id}.pdf");
    }
}