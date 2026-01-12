import AdminShell from "@/components/AdminShell";
import { requireMe } from "@/lib/auth";
import { redirect } from "next/navigation";
import { apiGet } from "@/lib/api";

export default async function AdminHome() {
  const me = await requireMe();
  if (!me) redirect("/login");

  const health = await apiGet("/health", { credentials: "include" });

  return (
    <AdminShell title="Dashboard" userLabel={`${me.name} (${me.email})`}>
      <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div className="bg-white border rounded p-4">
          <div className="font-medium">Quick Summary</div>
          <div className="text-sm text-slate-600 mt-2">
            Starter dashboard. Next iteration: counts customers/active/suspend, recent audit logs, tickets, etc.
          </div>
        </div>

        <div className="bg-white border rounded p-4">
          <div className="font-medium">API Health</div>
          <pre className="text-xs mt-2 bg-slate-50 border rounded p-2 overflow-auto">
            {JSON.stringify(health, null, 2)}
          </pre>
        </div>
      </div>
    </AdminShell>
  );
}