<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use Illuminate\Http\Request;

class AuditLogController extends Controller
{
    public function index(Request $request)
    {
        $q = AuditLog::query()->latest();

        if ($request->filled('action')) {
            $q->where('action', $request->string('action'));
        }

        if ($request->filled('resource_type')) {
            $q->where('resource_type', 'ilike', '%'.$request->string('resource_type').'%');
        }

        if ($request->filled('user_name')) {
            $q->where('users_name', 'ilike', '%'.$request->string('user_name').'%');
        }

        if ($request->filled('from')) {
            $q->where('created_at', '>=', $request->date('from'));
        }

        if ($request->filled('to')) {
            $q->where('created_at', '<=', $request->date('to')->endOfDay());
        }

        return $q->paginate(50);
    }
}