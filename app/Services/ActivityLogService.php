<?php

namespace App\Services;

use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ActivityLogService
{
    public function log(Request $request, string $activity)
    {
        $user = Auth::user();

        $log = new ActivityLog([
            'user_id' => $user->id,
            'activity' => $activity,
            'action' => $request->method() . ' ' . $request->path(),
            'performed_at' => now(),
        ]);

        $log->save();
    }
}
