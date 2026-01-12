<?php

namespace App\Http\Controllers\CRM;

use App\Http\Controllers\Controller;
use App\Models\Provisioning;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ProvisioningController extends Controller
{
    public function index()
    {
        return Provisioning::query()->latest()->paginate(20);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'subscriptions_id' => ['required','integer','exists:subscriptions,id'],
            'routers_id' => ['required','integer','exists:routers,id'],

            'device_brand' => ['nullable','string','max:100'],
            'device_type_device_sn' => ['nullable','string','max:150'],
            'device_mac' => [
                'nullable','string','max:50',
                // Keep it simple: allow AA:BB:CC:DD:EE:FF
                'regex:/^([0-9A-Fa-f]{2}:){5}[0-9A-Fa-f]{2}$/',
                Rule::unique('provisionings', 'device_mac')->whereNull('deleted_at'),
            ],

            'device_conn' => ['required', Rule::in(['PPPoE','Static-IP'])],

            // PPPoE
            'pppoe_name' => [
                'nullable','string','max:100',
                // unique per router when not deleted
                Rule::unique('provisionings', 'pppoe_name')
                    ->where(fn($q) => $q->where('routers_id', $request->input('routers_id'))->whereNull('deleted_at')),
            ],
            'pppoe_password' => ['nullable','string','max:200'],

            // Static
            'static_ip' => [
                'nullable','ip',
                Rule::unique('provisionings', 'static_ip')
                    ->where(fn($q) => $q->where('routers_id', $request->input('routers_id'))->whereNull('deleted_at')),
            ],
            'static_gateway' => ['nullable','ip'],

            'activation_date' => ['nullable','date'],
            'technisian_name' => ['nullable','string','max:100'],
            'document_speedtest' => ['nullable','string','max:255'],
            'technisian_notes' => ['nullable','string'],
        ]);

        // Conditional requirements
        if ($data['device_conn'] === 'PPPoE') {
            if (empty($data['pppoe_name'])) {
                return response()->json(['message' => 'pppoe_name is required for PPPoE'], 422);
            }
            // Ensure static fields not used
            $data['static_ip'] = null;
            $data['static_gateway'] = null;
        }

        if ($data['device_conn'] === 'Static-IP') {
            if (empty($data['static_ip'])) {
                return response()->json(['message' => 'static_ip is required for Static-IP'], 422);
            }
            // Ensure pppoe fields not used
            $data['pppoe_name'] = null;
            $data['pppoe_password'] = null;
        }

        $data['created_by'] = $request->user()->id;

        try {
            return Provisioning::create($data);
        } catch (\Throwable $e) {
            // DB unique index fallback (partial unique)
            return response()->json([
                'message' => 'Provisioning violates uniqueness constraints',
                'error' => $e->getMessage(),
            ], 409);
        }
    }

    public function show(Provisioning $provisioning)
    {
        return $provisioning;
    }

    public function update(Request $request, Provisioning $provisioning)
    {
        $data = $request->validate([
            'subscriptions_id' => ['sometimes','required','integer','exists:subscriptions,id'],
            'routers_id' => ['sometimes','required','integer','exists:routers,id'],

            'device_brand' => ['nullable','string','max:100'],
            'device_type_device_sn' => ['nullable','string','max:150'],
            'device_mac' => [
                'nullable','string','max:50',
                'regex:/^([0-9A-Fa-f]{2}:){5}[0-9A-Fa-f]{2}$/',
                Rule::unique('provisionings', 'device_mac')
                    ->ignore($provisioning->id)
                    ->whereNull('deleted_at'),
            ],

            'device_conn' => ['sometimes','required', Rule::in(['PPPoE','Static-IP'])],

            'pppoe_name' => [
                'nullable','string','max:100',
                Rule::unique('provisionings', 'pppoe_name')
                    ->ignore($provisioning->id)
                    ->where(function ($q) use ($request, $provisioning) {
                        $routerId = $request->input('routers_id', $provisioning->routers_id);
                        $q->where('routers_id', $routerId)->whereNull('deleted_at');
                    }),
            ],
            'pppoe_password' => ['nullable','string','max:200'],

            'static_ip' => [
                'nullable','ip',
                Rule::unique('provisionings', 'static_ip')
                    ->ignore($provisioning->id)
                    ->where(function ($q) use ($request, $provisioning) {
                        $routerId = $request->input('routers_id', $provisioning->routers_id);
                        $q->where('routers_id', $routerId)->whereNull('deleted_at');
                    }),
            ],
            'static_gateway' => ['nullable','ip'],

            'activation_date' => ['nullable','date'],
            'technisian_name' => ['nullable','string','max:100'],
            'document_speedtest' => ['nullable','string','max:255'],
            'technisian_notes' => ['nullable','string'],
        ]);

        // Determine effective conn after update
        $effectiveConn = $data['device_conn'] ?? $provisioning->device_conn;

        if ($effectiveConn === 'PPPoE') {
            $pppoeName = $data['pppoe_name'] ?? $provisioning->pppoe_name;
            if (empty($pppoeName)) {
                return response()->json(['message' => 'pppoe_name is required for PPPoE'], 422);
            }
            $data['static_ip'] = null;
            $data['static_gateway'] = null;
        }

        if ($effectiveConn === 'Static-IP') {
            $staticIp = $data['static_ip'] ?? $provisioning->static_ip;
            if (empty($staticIp)) {
                return response()->json(['message' => 'static_ip is required for Static-IP'], 422);
            }
            $data['pppoe_name'] = null;
            $data['pppoe_password'] = null;
        }

        try {
            $provisioning->update($data);
            return $provisioning;
        } catch (\Throwable $e) {
            return response()->json([
                'message' => 'Provisioning violates uniqueness constraints',
                'error' => $e->getMessage(),
            ], 409);
        }
    }

    public function destroy(Provisioning $provisioning)
    {
        $provisioning->delete();
        return response()->noContent();
    }
}