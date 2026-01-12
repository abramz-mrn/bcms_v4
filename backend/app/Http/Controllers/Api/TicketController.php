<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;

class TicketController extends Controller
{
    public function index()
    {
        return Ticket::query()
            ->with(['customer','product'])
            ->orderBy('id','desc')
            ->paginate(20);
    }

    public function store()
    {
        $data = request()->validate([
            'customer_id' => ['required','exists:customers,id'],
            'product_id' => ['nullable','exists:products,id'],
            'caller_name' => ['nullable','string','max:100'],
            'phone' => ['nullable','string','max:50'],
            'email' => ['nullable','email','max:200'],
            'category' => ['required','string','max:50'],
            'priority' => ['nullable','string','max:50'],
            'subject' => ['required','string','max:200'],
            'description' => ['nullable','string'],
            'status' => ['nullable','string','max:50'],
            'sla_due_date' => ['nullable','date'],
        ]);

        $data['ticket_number'] = 'TCK/'.now()->format('Ymd').'/'.Str::upper(Str::random(6));
        $data['status'] = $data['status'] ?? 'open';

        return Ticket::create($data)->load(['customer','product']);
    }

    public function show(Ticket $ticket) { return $ticket->load(['customer','product']); }

    public function update(Ticket $ticket)
    {
        $data = request()->validate([
            'status' => ['sometimes','string','max:50'],
            'assigned_to' => ['nullable','exists:users,id'],
            'assigned_at' => ['nullable','date'],
            'resolved_at' => ['nullable','date'],
            'closed_at' => ['nullable','date'],
            'resolution_notes' => ['nullable','string'],
            'customer_rating' => ['nullable','integer','min:1','max:5'],
            'customer_feedback' => ['nullable','string'],
        ]);

        $ticket->update($data);
        return $ticket->load(['customer','product']);
    }

    public function destroy(Ticket $ticket): JsonResponse
    {
        $ticket->delete();
        return response()->json(['ok' => true]);
    }
}