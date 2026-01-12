<?php

namespace App\Http\Controllers\Support;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use Illuminate\Http\Request;

class TicketController extends Controller
{
    public function index() { return Ticket::query()->latest()->paginate(20); }
    public function store(Request $request) {
        $data = $request->validate([
            'ticket_number' => ['required','string','max:50','unique:tickets,ticket_number'],
            'customers_id' => ['required','integer','exists:customers,id'],
            'products_id' => ['required','integer','exists:products,id'],
            'caller_name' => ['nullable','string'],
            'phone' => ['nullable','string'],
            'email' => ['nullable','email'],
            'category' => ['required','string'],
            'priority' => ['nullable','string'],
            'subject' => ['required','string'],
            'description' => ['required','string'],
            'status' => ['required','string'],
            'assigned_to' => ['nullable','integer'],
            'assigned_at' => ['nullable','date'],
            'resolved_at' => ['nullable','date'],
            'closed_at' => ['nullable','date'],
            'sla_due_date' => ['nullable','date'],
            'resolution_notes' => ['nullable','string'],
            'customer_rating' => ['nullable','integer'],
            'customer_feedback' => ['nullable','string'],
        ]);
        return Ticket::create($data);
    }
    public function show(Ticket $ticket) { return $ticket; }
    public function update(Request $request, Ticket $ticket) {
        $data = $request->validate([
            'category' => ['sometimes','required','string'],
            'priority' => ['nullable','string'],
            'subject' => ['sometimes','required','string'],
            'description' => ['sometimes','required','string'],
            'status' => ['sometimes','required','string'],
            'assigned_to' => ['nullable','integer'],
            'assigned_at' => ['nullable','date'],
            'resolved_at' => ['nullable','date'],
            'closed_at' => ['nullable','date'],
            'sla_due_date' => ['nullable','date'],
            'resolution_notes' => ['nullable','string'],
            'customer_rating' => ['nullable','integer'],
            'customer_feedback' => ['nullable','string'],
        ]);
        $ticket->update($data); return $ticket;
    }
    public function destroy(Ticket $ticket) { $ticket->delete(); return response()->noContent(); }
}