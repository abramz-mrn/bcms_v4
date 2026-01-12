<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Reminder;
use Illuminate\Http\JsonResponse;

class ReminderController extends Controller
{
    public function index()
    {
        $status = request('status');

        return Reminder::query()
            ->with(['invoice','template'])
            ->when($status, fn($q) => $q->where('status', $status))
            ->orderBy('id','desc')
            ->paginate(20);
    }

    public function store()
    {
        $data = request()->validate([
            'invoice_id' => ['required','exists:invoices,id'],
            'template_id' => ['required','exists:templates,id'],
            'channel' => ['required','string','max:20'],
            'trigger_type' => ['required','string','max:50'],
            'days_offset' => ['nullable','integer'],
            'scheduled_at' => ['required','date'],
            'status' => ['nullable','string','max:20'],
        ]);

        $data['created_by'] = request()->user()->id;
        $data['status'] = $data['status'] ?? 'pending';

        return Reminder::create($data)->load(['invoice','template']);
    }

    public function show(Reminder $reminder) { return $reminder->load(['invoice','template']); }

    public function update(Reminder $reminder)
    {
        $data = request()->validate([
            'scheduled_at' => ['sometimes','date'],
            'sent_at' => ['nullable','date'],
            'status' => ['sometimes','string','max:20'],
            'error_message' => ['nullable','string'],
        ]);

        $reminder->update($data);
        return $reminder->load(['invoice','template']);
    }

    public function destroy(Reminder $reminder): JsonResponse
    {
        $reminder->delete();
        return response()->json(['ok' => true]);
    }
}