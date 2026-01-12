<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Subscription;
use Illuminate\Http\JsonResponse;

class SubscriptionController extends Controller
{
    public function index()
    {
        return Subscription::query()
            ->with(['customer','product'])
            ->orderBy('id','desc')
            ->paginate(20);
    }

    public function store()
    {
        $data = request()->validate([
            'subscription_no' => ['required','string','max:50','unique:subscriptions,subscription_no'],
            'customer_id' => ['required','exists:customers,id'],
            'product_id' => ['required','exists:products,id'],
            'registration_date' => ['nullable','date'],
            'installation_address' => ['nullable','string'],
            'email_consent' => ['nullable','boolean'],
            'sms_consent' => ['nullable','boolean'],
            'whatsapp_consent' => ['nullable','boolean'],
            'status' => ['nullable','string','max:50'],
        ]);

        $data['created_by'] = request()->user()->id;

        return Subscription::create($data)->load(['customer','product']);
    }

    public function show(Subscription $subscription)
    {
        return $subscription->load(['customer','product']);
    }

    public function update(Subscription $subscription)
    {
        $data = request()->validate([
            'subscription_no' => ['sometimes','string','max:50',"unique:subscriptions,subscription_no,{$subscription->id}"],
            'customer_id' => ['sometimes','exists:customers,id'],
            'product_id' => ['sometimes','exists:products,id'],
            'registration_date' => ['nullable','date'],
            'installation_address' => ['nullable','string'],
            'email_consent' => ['nullable','boolean'],
            'sms_consent' => ['nullable','boolean'],
            'whatsapp_consent' => ['nullable','boolean'],
            'status' => ['nullable','string','max:50'],
        ]);

        $subscription->update($data);
        return $subscription->load(['customer','product']);
    }

    public function destroy(Subscription $subscription): JsonResponse
    {
        $subscription->delete();
        return response()->json(['ok' => true]);
    }
}