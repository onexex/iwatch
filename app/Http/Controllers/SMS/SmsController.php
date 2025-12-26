<?php

namespace App\Http\Controllers\SMS;

use Inertia\Inertia;
use App\Models\Barangay;
use App\Models\SmsMessage;
use App\Http\Controllers\Controller;

class SmsController extends Controller
{
    public function index()
    {
        $messages = SmsMessage::orderBy('created_at', 'desc')->get(); 
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
        // Pass messages to Inertia
        return Inertia::render('Sms', [
            'messages' => $messages,
            'regions' => $region,
            'provinces' => $province,
            'cities' => $municipality,
            'barangays' => $barangay,
        ]);
    }
}