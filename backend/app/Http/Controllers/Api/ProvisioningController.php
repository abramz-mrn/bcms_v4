<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Provisioning;
use App\Services\Mikrotik\MikrotikService;
use Illuminate\Http\JsonResponse;

class ProvisioningController extends Controller
{
    public function index()
    {
        return Provisioning::query()
            ->with(['router'])
            ->orderBy('id','desc')
            ->paginate(20);
    }

    public function store()
    {
        $data = request()->validate([
            'subscription_id' => ['required','exists:subscriptions,id'],
            'subscription_no' => ['nullable','string','max:50'],
            'router_id' => ['required','exists:routers,id'],
            'device_conn' => ['required','string','max:20'], // PPPoE|Static-IP
            'device_brand' => ['nullable','string','max:100'],
            'device_type' => ['nullable','string','max:100'],
            'device_sn' => ['nullable','string','max:100'],
            'device_mac' => ['nullable','string','max:50'],
            'pppoe_name' => ['nullable','string','max:100'],
            'pppoe_password' => ['nullable','string','max:100'],
            'initial_name' => ['nullable','string','max:100'],
            'static_ip' => ['nullable','ip'],
            'static_gateway' => ['nullable','ip'],
            'activation_date' => ['nullable','date'],
            'technisian_name' => ['nullable','string','max:100'],
            'technisian_notes' => ['nullable','string'],
        ]);

        $data['created_by'] = request()->user()->id;

        return Provisioning::create($data);
    }

    public function show(Provisioning $provisioning) { return $provisioning; }

    public function update(Provisioning $provisioning)
    {
        $data = request()->validate([
            'router_id' => ['sometimes','exists:routers,id'],
            'device_conn' => ['sometimes','string','max:20'],
            'device_brand' => ['nullable','string','max:100'],
            'device_type' => ['nullable','string','max:100'],
            'device_sn' => ['nullable','string','max:100'],
            'device_mac' => ['nullable','string','max:50'],
            'pppoe_name' => ['nullable','string','max:100'],
            'pppoe_password' => ['nullable','string','max:100'],
            'initial_name' => ['nullable','string','max:100'],
            'static_ip' => ['nullable','ip'],
            'static_gateway' => ['nullable','ip'],
            'activation_date' => ['nullable','date'],
            'technisian_name' => ['nullable','string','max:100'],
            'technisian_notes' => ['nullable','string'],
        ]);

        $provisioning->update($data);
        return $provisioning;
    }

    public function destroy(Provisioning $provisioning): JsonResponse
    {
        $provisioning->delete();
        return response()->json(['ok' => true]);
    }

    public function pingTest(Provisioning $provisioning, MikrotikService $mikrotik): JsonResponse
    {
        $provisioning->load('router');

        $target = $provisioning->device_conn === 'Static-IP'
            ? ($provisioning->static_ip ?? '8.8.8.8')
            : ($provisioning->static_ip ?? '8.8.8.8'); // PPPoE: could ping CPE mgmt IP if any

        $result = $mikrotik->pingTest($provisioning->router, $target, 5);

        return response()->json(['ok' => true, 'result' => $result]);
    }
}