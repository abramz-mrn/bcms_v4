<?php
// ...snip within applyToProvisioning just before AuditLog::create...

        $extra = [];
        if ($prov->device_conn === 'Static-IP') {
            $extra = [
                'queue_name' => \App\Services\Mikrotik\QueueName::forProvisioning($prov),
                'target' => $prov->static_ip ? ($prov->static_ip.'/32') : null,
            ];
        }

        AuditLog::query()->create([
            // ...
            'new_value' => array_merge([
                'subscription_id' => $subscription->id,
                'provisioning_id' => $prov->id,
                'product_id' => $subscription->products_id,
                'router_id' => $prov->routers_id,
                'decision' => $decision,
                'applied_action' => $action,
                'driver' => $client->driverName(),
            ], $extra),
            // ...
        ]);