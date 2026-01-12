<?php

namespace App\Services\Mikrotik;

use App\Models\Provisioning;

class QueueName
{
    /**
     * Naming convention (default):
     * SUB-{subscription_id}
     */
    public static function forProvisioning(Provisioning $prov): string
    {
        return 'SUB-' . $prov->subscriptions_id;
    }
}