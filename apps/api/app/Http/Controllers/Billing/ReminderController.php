<?php

namespace App\Http\Controllers\Billing;

use App\Http\Controllers\Controller;
use App\Models\Reminder;
use Illuminate\Http\Request;

class ReminderController extends Controller
{
    public function index() { return Reminder::query()->latest()->paginate(20); }
    public function store(Request $request) {
        $data = $request->validate([
            'invoices_id' => ['required','integer','exists:invoices,id'],
            'templates_id' => ['required','integer','exists:templates,id'],
            'channel' => ['required','string'],
            'trigger_type' => ['required','string'],
            'days_offset' => ['required','integer'],
            'scheduled_at' => ['nullable','date'],
            'sent_at' => ['nullable','date'],
            'status' => ['required','string'],
            'error_message' => ['nullable','string'],
        ]);
        $data['created_by'] = $request->user()->id;
        return Reminder::create($data);
    }
    public function show(Reminder $reminder) { return $reminder; }
    public function update(Request $request, Reminder $reminder) {
        $data = $request->validate([
            'templates_id' => ['sometimes','required','integer','exists:templates,id'],
            'channel' => ['sometimes','required','string'],
            'trigger_type' => ['sometimes','required','string'],
            'days_offset' => ['sometimes','required','integer'],
            'scheduled_at' => ['nullable','date'],
            'sent_at' => ['nullable','date'],
            'status' => ['sometimes','required','string'],
            'error_message' => ['nullable','string'],
        ]);
        $reminder->update($data); return $reminder;
    }
    public function destroy(Reminder $reminder) { $reminder->delete(); return response()->noContent(); }
}