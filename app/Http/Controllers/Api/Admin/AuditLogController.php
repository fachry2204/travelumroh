<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use Illuminate\Http\Request;

class AuditLogController extends Controller
{
    public function index(Request $request)
    {
        $logs = AuditLog::with('user:id,name,role_type')
            ->orderByDesc('created_at')
            ->paginate(50);

        return response()->json([
            'success' => true,
            'data' => $logs
        ]);
    }
}
