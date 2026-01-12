<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\InternetService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class InternetServiceController extends Controller
{
    public function index(Request $request)
    {
        $q = InternetService::query()
            ->with(['product:id,code,name,billing_cycle', 'router:id,name,ip_address,location'])
            ->latest();

        if ($request->filled('search')) {
            $s = trim((string) $request->string('search'));
            $q->whereHas('product', function ($qq) use ($s) {
                $qq->where('code', 'ilike', "%{$s}%")
                   ->orWhere('name', 'ilike', "%{$s}%");
            })->orWhereHas('router', function ($qq) use ($s) {
                $qq->where('name', 'ilike', "%{$s}%")
                   ->orWhere('location', 'ilike', "%{$s}%")
                   ->orWhere('ip_address', 'ilike', "%{$s}%");
            });
        }

        $perPage = (int) ($request->integer('per_page') ?: 20);
        $perPage = max(5, min(100, $perPage));

        return $q->paginate($perPage);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'products_id' => [
                'required','integer','exists:products,id',
                // ensure 1 product has 1 internet_service (non-deleted)
                Rule::unique('internet_services', 'products_id')->whereNull('deleted_at'),
            ],
            'routers_id' => ['required','integer','exists:routers,id'],
            'profile' => ['nullable','string','max:100'],

            // Example format: 5M/5M (we keep flexible)
            'rate_limit' => ['nullable','string','max:50'],
            'limit_at' => ['nullable','string','max:50'],
            'priority' => ['nullable','string','max:50'],

            'start_date' => ['nullable','date'],
            'due_date' => ['nullable','date'],

            // days after due date
            'auto_soft_limit' => ['required','integer','min:0','max:365'],
            'auto_suspend' => ['required','integer','min:0','max:365'],
        ]);

        // policy sanity: suspend should be >= soft_limit (recommended)
        if ($data['auto_suspend'] > 0 && $data['auto_soft_limit'] > 0 && $data['auto_suspend'] < $data['auto_soft_limit']) {
            return response()->json([
                'message' => 'auto_suspend should be >= auto_soft_limit (or set one of them to 0).',
                'code' => 'INVALID_POLICY',
            ], 422);
        }

        try {
            return InternetService::create($data);
        } catch (\Throwable $e) {
            return response()->json([
                'message' => 'Internet service violates uniqueness constraints.',
                'code' => 'INTERNET_SERVICE_UNIQUE_VIOLATION',
                'error' => $e->getMessage(),
            ], 409);
        }
    }

    public function show(InternetService $internetService)
    {
        return $internetService->load(['product', 'router']);
    }

    public function update(Request $request, InternetService $internetService)
    {
        $data = $request->validate([
            'products_id' => [
                'sometimes','required','integer','exists:products,id',
                Rule::unique('internet_services', 'products_id')
                    ->ignore($internetService->id)
                    ->whereNull('deleted_at'),
            ],
            'routers_id' => ['sometimes','required','integer','exists:routers,id'],
            'profile' => ['nullable','string','max:100'],
            'rate_limit' => ['nullable','string','max:50'],
            'limit_at' => ['nullable','string','max:50'],
            'priority' => ['nullable','string','max:50'],
            'start_date' => ['nullable','date'],
            'due_date' => ['nullable','date'],
            'auto_soft_limit' => ['sometimes','required','integer','min:0','max:365'],
            'auto_suspend' => ['sometimes','required','integer','min:0','max:365'],
        ]);

        $effectiveSoft = (int) ($data['auto_soft_limit'] ?? $internetService->auto_soft_limit);
        $effectiveSuspend = (int) ($data['auto_suspend'] ?? $internetService->auto_suspend);

        if ($effectiveSuspend > 0 && $effectiveSoft > 0 && $effectiveSuspend < $effectiveSoft) {
            return response()->json([
                'message' => 'auto_suspend should be >= auto_soft_limit (or set one of them to 0).',
                'code' => 'INVALID_POLICY',
            ], 422);
        }

        try {
            $internetService->update($data);
            return $internetService->load(['product','router']);
        } catch (\Throwable $e) {
            return response()->json([
                'message' => 'Internet service violates uniqueness constraints.',
                'code' => 'INTERNET_SERVICE_UNIQUE_VIOLATION',
                'error' => $e->getMessage(),
            ], 409);
        }
    }

    public function destroy(InternetService $internetService)
    {
        $internetService->delete();
        return response()->noContent();
    }
}