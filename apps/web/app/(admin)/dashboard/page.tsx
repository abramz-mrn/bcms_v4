"use client";

import { useEffect, useState } from "react";

type DashboardSummary = {
  meta: { generated_at: string };
  totals: {
    customers: number;
    active_customers: number;
    sla_due_soon_tickets: number;
  };
  lists: {
    suspended_customers: Array<any>;
    recent_activities: Array<any>;
    open_tickets: Array<any>;
  };
};

export default function DashboardPage() {
  const [data, setData] = useState<DashboardSummary | null>(null);
  const [err, setErr] = useState<string>("");

  useEffect(() => {
    (async () => {
      const base = process.env.NEXT_PUBLIC_API_BASE_URL || "http://127.0.0.1/api";
      const r = await fetch(`${base}/v1/dashboard/summary`, {
        credentials: "include",
        cache: "no-store"
      });

      if (!r.ok) {
        setErr(`${r.status} ${r.statusText}: ${await r.text()}`);
        return;
      }
      setData(await r.json());
    })();
  }, []);

  return (
    <div className="space-y-6">
      <div>
        <h1 className="text-2xl font-semibold">Dashboard</h1>
        <p className="text-sm text-slate-600">
          Ringkasan pelanggan, suspend, aktivitas user, dan tickets.
        </p>
      </div>

      {err && (
        <div className="p-4 bg-red-50 border border-red-200 rounded-xl text-sm text-red-700">
          {err}
          <div className="mt-2 text-xs text-red-600">
            Pastikan sudah login dan group punya permission <code>reports.view</code>.
          </div>
        </div>
      )}

      {data && (
        <>
          <section className="grid grid-cols-1 md:grid-cols-3 gap-4">
            <Card title="Total Customers" value={data.totals.customers} />
            <Card title="Active Customers" value={data.totals.active_customers} />
            <Card title="SLA due ≤ 24h (tickets)" value={data.totals.sla_due_soon_tickets} />
          </section>

          <section className="grid grid-cols-1 lg:grid-cols-2 gap-4">
            <Panel title="Suspended Customers (Top 10)">
              <Table
                columns={["code", "name", "phone", "group_area"]}
                rows={data.lists.suspended_customers}
              />
            </Panel>

            <Panel title="Open Tickets (Top 10)">
              <Table
                columns={["ticket_number", "category", "priority", "status", "sla_due_date"]}
                rows={data.lists.open_tickets}
              />
            </Panel>
          </section>

          <section>
            <Panel title="Recent User Activity (Audit Logs Top 10)">
              <Table
                columns={["created_at", "users_name", "action", "resource_type", "description"]}
                rows={data.lists.recent_activities}
              />
            </Panel>
          </section>

          <div className="text-xs text-slate-500">
            Generated at: {data.meta.generated_at}
          </div>
        </>
      )}
    </div>
  );
}

function Card({ title, value }: { title: string; value: number }) {
  return (
    <div className="bg-white border rounded-2xl p-5 shadow-sm">
      <div className="text-sm text-slate-600">{title}</div>
      <div className="mt-2 text-3xl font-semibold">{value}</div>
    </div>
  );
}

function Panel({ title, children }: { title: string; children: React.ReactNode }) {
  return (
    <div className="bg-white border rounded-2xl p-5 shadow-sm">
      <div className="font-semibold">{title}</div>
      <div className="mt-3">{children}</div>
    </div>
  );
}

function Table({ columns, rows }: { columns: string[]; rows: any[] }) {
  return (
    <div className="overflow-auto border rounded-xl">
      <table className="min-w-full text-sm">
        <thead className="bg-slate-50">
          <tr>
            {columns.map((c) => (
              <th key={c} className="text-left px-3 py-2 border-b font-medium text-slate-700">
                {c}
              </th>
            ))}
          </tr>
        </thead>
        <tbody>
          {rows?.length ? (
            rows.map((r, idx) => (
              <tr key={idx} className="odd:bg-white even:bg-slate-50">
                {columns.map((c) => (
                  <td key={c} className="px-3 py-2 border-b text-slate-800 whitespace-nowrap">
                    {String(r?.[c] ?? "")}
                  </td>
                ))}
              </tr>
            ))
          ) : (
            <tr>
              <td className="px-3 py-4 text-slate-500" colSpan={columns.length}>
                No data
              </td>
            </tr>
          )}
        </tbody>
      </table>
    </div>
  );
}