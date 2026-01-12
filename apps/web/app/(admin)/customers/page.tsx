"use client";

import { useMe } from "@/lib/useMe";
import { hasPermission } from "@/lib/permissions";
// ...existing imports...

export default function CustomersPage() {
  const me = useMe();
  const perms = me?.group?.permissions;
  const canManage = hasPermission(perms, "customers.manage");

  // ...snip...

  return (
    <div className="space-y-4">
      <div className="flex items-end justify-between gap-3 flex-wrap">
        <div>
          <h1 className="text-2xl font-semibold">Customers</h1>
          <p className="text-sm text-slate-600">List customer + search + pagination (starter).</p>
        </div>

        <div className="flex items-center gap-2">
          {canManage && (
            <a className="px-3 py-2 rounded-lg bg-slate-900 text-white text-sm" href="/customers/new">
              + New Customer
            </a>
          )}
          <div className="text-sm text-slate-500">
            Total: {data?.total ?? "-"}
          </div>
        </div>
      </div>
      {/* ...snip... */}
    </div>
  );
}