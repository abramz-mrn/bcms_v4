<?php

namespace App\Http\Controllers\CRM;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        $q = Customer::query()->latest();

        if ($request->filled('search')) {
            $s = trim((string) $request->string('search'));
            $q->where(function ($qq) use ($s) {
                $qq->where('code', 'ilike', "%{$s}%")
                   ->orWhere('name', 'ilike', "%{$s}%")
                   ->orWhere('phone', 'ilike', "%{$s}%")
                   ->orWhere('email', 'ilike', "%{$s}%")
                   ->orWhere('group_area', 'ilike', "%{$s}%");
            });
        }

        $perPage = (int) ($request->integer('per_page') ?: 20);
        $perPage = max(5, min(100, $perPage));

        return $q->paginate($perPage);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'code' => ['required','string','max:50','unique:customers,code'],
            'name' => ['required','string'],
            'id_card_number' => ['nullable','string'],
            'address' => ['nullable','string'],
            'city' => ['nullable','string'],
            'state' => ['nullable','string'],
            'pos' => ['nullable','string'],
            'group_area' => ['nullable','string'],
            'phone' => ['nullable','string'],
            'email' => ['nullable','email'],
            'document_id_card' => ['nullable','string'],
            'notes' => ['nullable','string'],
        ]);
        $data['created_by'] = $request->user()->id;

        return Customer::create($data);
    }

    public function show(Customer $customer)
    {
        // load tabs
        return $customer->load([
            'subscriptions.product',
            'subscriptions.provisionings.router',
        ]);
    }

    public function update(Request $request, Customer $customer)
    {
        $data = $request->validate([
            'code' => ['sometimes','required','string','max:50','unique:customers,code,'.$customer->id],
            'name' => ['sometimes','required','string'],
            'id_card_number' => ['nullable','string'],
            'address' => ['nullable','string'],
            'city' => ['nullable','string'],
            'state' => ['nullable','string'],
            'pos' => ['nullable','string'],
            'group_area' => ['nullable','string'],
            'phone' => ['nullable','string'],
            'email' => ['nullable','email'],
            'document_id_card' => ['nullable','string'],
            'notes' => ['nullable','string'],
        ]);
        $customer->update($data);

        return $customer;
    }

    public function destroy(Customer $customer)
    {
        $customer->delete();
        return response()->noContent();
    }
}