<?php

namespace App\Http\Controllers;

use App\Models\Incident;
use Illuminate\Http\Request;

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
        return response()->json(Incident::with('barangay')->get());
    }
}