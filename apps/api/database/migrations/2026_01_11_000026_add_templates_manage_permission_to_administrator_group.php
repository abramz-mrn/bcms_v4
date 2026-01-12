<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        $groupName = 'Administrator';
        $perm = 'templates.manage';

        $group = DB::table('users_groups')
            ->select('id', 'permissions')
            ->where('name', $groupName)
            ->first();

        if (!$group) {
            return;
        }

        $arr = [];
        if (is_string($group->permissions) && trim($group->permissions) !== '') {
            $decoded = json_decode($group->permissions, true);
            if (is_array($decoded)) $arr = $decoded;
        } elseif (is_array($group->permissions)) {
            $arr = $group->permissions;
        }

        if (!in_array($perm, $arr, true)) {
            $arr[] = $perm;
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
        $groupName = 'Administrator';
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