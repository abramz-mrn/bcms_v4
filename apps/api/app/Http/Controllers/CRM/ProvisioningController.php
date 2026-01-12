<?php

namespace App\Http\Controllers\CRM;

use App\Http\Controllers\Controller;
use App\Models\Provisioning;
use Illuminate\Http\Request;

class ProvisioningController extends Controller
{
    public function index() { return Provisioning::query()->latest()->paginate(20); }
    public function store(Request $request) {
        $data = $request->validate([
            'subscriptions_id' => ['required','integer','exists:subscriptions,id'],
            'routers_id' => ['required','integer','exists:routers,id'],
            'device_brand' => ['nullable','string'],
            'device_type_device_sn' => ['nullable','string'],
            'device_mac' => ['nullable','string'],
            'device_conn' => ['required','string'], // PPPoE / Static-IP
            'pppoe_name' => ['nullable','string'],
            'pppoe_password' => ['nullable','string'],
            'static_ip' => ['nullable','ip'],
            'static_gateway' => ['nullable','ip'],
            'activation_date' => ['nullable','date'],
            'technisian_name' => ['nullable','string'],
            'document_speedtest' => ['nullable','string'],
            'technisian_notes' => ['nullable','string'],
        ]);
        $data['created_by'] = $request->user()->id;
        return Provisioning::create($data);
    }
    public function show(Provisioning $provisioning) { return $provisioning; }
    public function update(Request $request, Provisioning $provisioning) {
        $data = $request->validate([
            'subscriptions_id' => ['sometimes','required','integer','exists:subscriptions,id'],
            'routers_id' => ['sometimes','required','integer','exists:routers,id'],
            'device_brand' => ['nullable','string'],
            'device_type_device_sn' => ['nullable','string'],
            'device_mac' => ['nullable','string'],
            'device_conn' => ['sometimes','required','string'],
            'pppoe_name' => ['nullable','string'],
            'pppoe_password' => ['nullable','string'],
            'static_ip' => ['nullable','ip'],
            'static_gateway' => ['nullable','ip'],
            'activation_date' => ['nullable','date'],
            'technisian_name' => ['nullable','string'],
            'document_speedtest' => ['nullable','string'],
            'technisian_notes' => ['nullable','string'],
        ]);
        $provisioning->update($data); return $provisioning;
    }
    public function destroy(Provisioning $provisioning) { $provisioning->delete(); return response()->noContent(); }
}