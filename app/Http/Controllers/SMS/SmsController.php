<?php

namespace App\Http\Controllers\SMS;

use Inertia\Inertia;
use App\Models\Barangay;
use App\Models\SmsMessage;
use Illuminate\Http\Request;
use App\Models\Classification;
use App\Http\Controllers\Controller;
use App\Models\Incident;
use Illuminate\Support\Facades\Auth;

class SmsController extends Controller
{
    public function index()
    {
        $messages = SmsMessage::orderBy('created_at', 'desc')
            ->where('is_read', 0)
            ->get(); 
        $region = Barangay::select('region')
            ->whereIn('region', ['Region X (Northern Mindanao)', 'Region IX (Zamboanga Peninsula)', 'Region XII (SOCCSKSARGEN)', 'Bangsamoro Autonomous Region In Muslim Mindanao (BARMM)', 'Region XI (Davao Region)', 'Region XIII (Caraga)'])
            ->distinct()->get();
        $province = Barangay::select('province', 'region')
            ->whereIn('region', ['Region X (Northern Mindanao)', 'Region IX (Zamboanga Peninsula)', 'Region XII (SOCCSKSARGEN)', 'Bangsamoro Autonomous Region In Muslim Mindanao (BARMM)', 'Region XI (Davao Region)', 'Region XIII (Caraga)'])
            ->orderBy('province')
            ->distinct()->get();

        $municipality = Barangay::select('city_municipality', 'province')
            ->whereIn('region', ['Region X (Northern Mindanao)', 'Region IX (Zamboanga Peninsula)', 'Region XII (SOCCSKSARGEN)', 'Bangsamoro Autonomous Region In Muslim Mindanao (BARMM)', 'Region XI (Davao Region)', 'Region XIII (Caraga)'])
            ->orderBy('city_municipality')
            ->distinct()->get();

        $barangay = Barangay::select('barangay', 'id', 'city_municipality')
            ->whereIn('region', ['Region X (Northern Mindanao)', 'Region IX (Zamboanga Peninsula)', 'Region XII (SOCCSKSARGEN)', 'Bangsamoro Autonomous Region In Muslim Mindanao (BARMM)', 'Region XI (Davao Region)', 'Region XIII (Caraga)'])
            ->orderBy('barangay')
            ->distinct()->get();
        
        $classifications = Classification::all();

        return Inertia::render('Sms', [
            'messages' => $messages,
            'regions' => $region,
            'provinces' => $province,
            'cities' => $municipality,
            'barangays' => $barangay,
            'classifications' => $classifications,
        ]);
    }

    public function store(Request $request)
    {
        $classification = Classification::where('id', $request->classificationId)
            ->first();

        $incident = Incident::create([
            'sms_id' => $request->smsId,
            'address_id' => $request->selectedBarangay,
            'description' => $request->subject,
            'type' =>  $classification ? $classification->name : $request->classificationId,
            'classification_id' => $request->classificationId,
            'file_number' => $request->file_number,
            'reference' => $request->reference,
            'subject' => $request->subject,
            'date_of_report' => $request->dateOfReport,
            'reporter' => $request->reporter,
            'designation' => $request->designation,
            'evaluation' => $request->evaluation,
            'source' => $request->source,
            'date_acquired' => $request->dateAcquired,
            'manner_acquired' => $request->mannerAcquired,
            'information_proper' => $request->informationProper,
            'analysis' => $request->analysis,
            'actions' => $request->actions,
            'created_by' => Auth::user()->id,
        ]);

        SmsMessage::where('id', $request->smsId)
            ->update(['is_read' => 1]);

        return redirect()->back();
    }
}