<?php

namespace App\Http\Controllers\V1\ThirdParty;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\OtpLog;

class TwilioWebhookController extends Controller
{
    public function statusCallback(Request $request)
    {
        $messageSid = $request->input('MessageSid');
        $status = $request->input('MessageStatus');

        $log = OtpLog::where('message_sid', $messageSid)->first();
        if ($log) {
            $log->status = $status;
            $log->payload = json_encode($request->all());
            $log->save();
        }

        return response('Callback received', 200);
    }
}
