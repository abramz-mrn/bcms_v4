<?php

namespace App\Http\Controllers\CRM;

use App\Http\Controllers\Controller;
use App\Models\Subscription;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class SubscriptionController extends Controller
{
    public function index()
    {
        return Subscription::query()->latest()->paginate(20);
    }

    public function store(Request $request)
    {
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
            'status' => ['required', Rule::in(['Registered','Active','Soft-Limit','Suspend','Terminated'])],
        ]);

        // Business rule: prevent double active-like subscription for same customer+product
        if (in_array($data['status'], ['Active','Soft-Limit','Suspend'], true)) {
            $exists = Subscription::query()
                ->where('customers_id', $data['customers_id'])
                ->where('products_id', $data['products_id'])
                ->whereIn('status', ['Active','Soft-Limit','Suspend'])
                ->whereNull('deleted_at')
                ->exists();

            if ($exists) {
                return response()->json([
                    'message' => 'Duplicate active subscription for the same customer and product is not allowed.',
                    'code' => 'DUPLICATE_ACTIVE_SUBSCRIPTION',
                ], 409);
            }
        }

        $data['created_by'] = $request->user()->id;

        try {
            return Subscription::create($data);
        } catch (\Throwable $e) {
            // DB unique partial index fallback
            return response()->json([
                'message' => 'Subscription violates uniqueness constraints.',
                'code' => 'SUBSCRIPTION_UNIQUE_VIOLATION',
                'error' => $e->getMessage(),
            ], 409);
        }
    }

    public function show(Subscription $subscription)
    {
        return $subscription;
    }

    public function update(Request $request, Subscription $subscription)
    {
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
            'status' => ['sometimes','required', Rule::in(['Registered','Active','Soft-Limit','Suspend','Terminated'])],
        ]);

        $effectiveCustomerId = (int) ($data['customers_id'] ?? $subscription->customers_id);
        $effectiveProductId  = (int) ($data['products_id'] ?? $subscription->products_id);
        $effectiveStatus     = (string) ($data['status'] ?? $subscription->status);

        if (in_array($effectiveStatus, ['Active','Soft-Limit','Suspend'], true)) {
            $exists = Subscription::query()
                ->where('customers_id', $effectiveCustomerId)
                ->where('products_id', $effectiveProductId)
                ->whereIn('status', ['Active','Soft-Limit','Suspend'])
                ->whereNull('deleted_at')
                ->where('id', '!=', $subscription->id)
                ->exists();

            if ($exists) {
                return response()->json([
                    'message' => 'Duplicate active subscription for the same customer and product is not allowed.',
                    'code' => 'DUPLICATE_ACTIVE_SUBSCRIPTION',
                ], 409);
            }
        }

        try {
            $subscription->update($data);
            return $subscription;
        } catch (\Throwable $e) {
            return response()->json([
                'message' => 'Subscription violates uniqueness constraints.',
                'code' => 'SUBSCRIPTION_UNIQUE_VIOLATION',
                'error' => $e->getMessage(),
            ], 409);
        }
    }

    public function destroy(Subscription $subscription)
    {
        $subscription->delete();
        return response()->noContent();
    }
}