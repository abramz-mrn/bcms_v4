<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        // PPPoE unique per router (only when not deleted, and pppoe_name is not null)
        DB::statement("
            CREATE UNIQUE INDEX IF NOT EXISTS provisionings_router_pppoe_unique
            ON provisionings (routers_id, pppoe_name)
            WHERE deleted_at IS NULL AND pppoe_name IS NOT NULL
        ");

        // Static IP unique per router (only when not deleted, and static_ip is not null)
        DB::statement("
            CREATE UNIQUE INDEX IF NOT EXISTS provisionings_router_static_ip_unique
            ON provisionings (routers_id, static_ip)
            WHERE deleted_at IS NULL AND static_ip IS NOT NULL
        ");

        // Device MAC unique global (only when not deleted, and device_mac is not null)
        DB::statement("
            CREATE UNIQUE INDEX IF NOT EXISTS provisionings_device_mac_unique
            ON provisionings (device_mac)
            WHERE deleted_at IS NULL AND device_mac IS NOT NULL
        ");
    }

    public function down(): void
    {
        DB::statement("DROP INDEX IF EXISTS provisionings_router_pppoe_unique");
        DB::statement("DROP INDEX IF EXISTS provisionings_router_static_ip_unique");
        DB::statement("DROP INDEX IF EXISTS provisionings_device_mac_unique");
    }
};