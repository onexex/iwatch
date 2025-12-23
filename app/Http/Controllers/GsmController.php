<?php

namespace App\Http\Controllers;

use App\Models\SmsMessage; // Assuming you have an SmsMessage model
use Illuminate\Support\Facades\Process;

class GsmController extends Controller
{
   public function fetchMessages()
{
    // Use base_path to ensure the scheduler always finds the file
    // $result = Process::run("python " . base_path('gsm_receive.py'));
      $result = Process::run("python gsm_receive.py");

    if ($result->successful()) {
        $inbox = json_decode($result->output(), true);

        if (is_array($inbox)) {
            foreach ($inbox as $sms) {
                // Ensure we handle potential nulls or empty data
                if (isset($sms['time'], $sms['sender'], $sms['message'])) {
                    SmsMessage::updateOrCreate(
                        [
                            'received_at' => $sms['time'], 
                            'sender' => $sms['sender']
                        ],
                        [
                            'message' => $sms['message']
                        ]
                    );
                }
            }
            return response()->json(['status' => 'success', 'count' => count($inbox)]);
        }
    }

    \Illuminate\Support\Facades\Log::error("GSM Fetch Failed: " . $result->errorOutput());
    return response()->json(['error' => 'Sync failed'], 500);
}
}