<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        $groupName = 'Super Admin';
        $perm = 'templates.manage';

        $group = DB::table('users_groups')
            ->select('id', 'permissions')
            ->where('name', $groupName)
            ->first();

        if (!$group) {
            // Group not found; do nothing to avoid breaking deploy.
            return;
        }

        $permissions = $group->permissions;

        // permissions column is JSON array; decode safely
        $arr = [];
        if (is_string($permissions) && trim($permissions) !== '') {
            $decoded = json_decode($permissions, true);
            if (is_array($decoded)) {
                $arr = $decoded;
            }
        } elseif (is_array($permissions)) {
            // in case driver returns array already
            $arr = $permissions;
        }

        if (!in_array($perm, $arr, true)) {
            $arr[] = $perm;
            // normalize: unique + reindex
            $arr = array_values(array_unique($arr));

            DB::table('users_groups')
                ->where('id', $group->id)
                ->update([
                    'permissions' => json_encode($arr),
                    'updated_at' => now(),
                ]);
        }
    }

    public function down(): void
    {
        $groupName = 'Super Admin';
        $perm = 'templates.manage';

        $group = DB::table('users_groups')
            ->select('id', 'permissions')
            ->where('name', $groupName)
            ->first();

        if (!$group) return;

        $arr = [];
        if (is_string($group->permissions) && trim($group->permissions) !== '') {
            $decoded = json_decode($group->permissions, true);
            if (is_array($decoded)) $arr = $decoded;
        } elseif (is_array($group->permissions)) {
            $arr = $group->permissions;
        }

        $arr = array_values(array_filter($arr, fn ($p) => $p !== $perm));

        DB::table('users_groups')
            ->where('id', $group->id)
            ->update([
                'permissions' => json_encode($arr),
                'updated_at' => now(),
            ]);
    }
};