<?php

namespace App\Http\Controllers\CRM;

use App\Http\Controllers\Controller;
use App\Models\Subscription;
use Illuminate\Http\Request;

class SubscriptionController extends Controller
{
    public function index() { return Subscription::query()->latest()->paginate(20); }
    public function store(Request $request) {
        $data = $request->validate([
            'customers_id' => ['required','integer','exists:customers,id'],
            'products_id' => ['required','integer','exists:products,id'],
            'registration_date' => ['required','date'],
            'email_consent' => ['required','boolean'],
            'sms_consent' => ['required','boolean'],
            'whatsapp_consent' => ['required','boolean'],
            'document_sf' => ['nullable','string'],
            'document_asf' => ['nullable','string'],
            'document_pks' => ['nullable','string'],
            'status' => ['required','string'],
        ]);
        $data['created_by'] = $request->user()->id;
        return Subscription::create($data);
    }
    public function show(Subscription $subscription) { return $subscription; }
    public function update(Request $request, Subscription $subscription) {
        $data = $request->validate([
            'customers_id' => ['sometimes','required','integer','exists:customers,id'],
            'products_id' => ['sometimes','required','integer','exists:products,id'],
            'registration_date' => ['sometimes','required','date'],
            'email_consent' => ['sometimes','required','boolean'],
            'sms_consent' => ['sometimes','required','boolean'],
            'whatsapp_consent' => ['sometimes','required','boolean'],
            'document_sf' => ['nullable','string'],
            'document_asf' => ['nullable','string'],
            'document_pks' => ['nullable','string'],
            'status' => ['sometimes','required','string'],
        ]);
        $subscription->update($data); return $subscription;
    }
    public function destroy(Subscription $subscription) { $subscription->delete(); return response()->noContent(); }
}