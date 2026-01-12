"use client";

import AdminShell from "@/components/AdminShell";
import Link from "next/link";
import { useEffect, useState } from "react";
import { apiGet, apiPost } from "@/lib/api";
import { CrudMessage, DangerButton, useCrudSubmit } from "@/components/crud";
import { ensureCsrfCookie } from "@/lib/api";

export default function ProvisioningsPage() {
  const [rows, setRows] = useState<any[]>([]);
  const { msg, setMsg, remove } = useCrudSubmit();

  async function load() {
    const data = await apiGet("/provisionings", { credentials: "include" });
    setRows(data.data);
  }

  useEffect(() => {
    load().catch((e) => setMsg(e.message));
  }, [setMsg]);

  async function pingTest(id: number) {
    setMsg("");
    try {
      await ensureCsrfCookie();
      const res = await apiPost(`/provisionings/${id}/ping-test`, {}, { credentials: "include" });
      setMsg(`Ping OK: loss=${res.result.loss_percent}% received=${res.result.received}/${res.result.sent}`);
    } catch (e: any) {
      setMsg(e?.message ?? "Ping failed");
    }
  }

  return (
    <AdminShell title="Provisionings" userLabel="(session)">
      <div className="flex justify-between mb-4">
        <CrudMessage msg={msg} />
        <Link className="px-4 py-2 rounded bg-slate-900 text-white" href="/admin/provisionings/new">
          Add Provisioning
        </Link>
      </div>

      <div className="bg-white border rounded overflow-auto">
        <table className="w-full text-sm">
          <thead className="bg-slate-100">
            <tr>
              <th className="text-left p-3">Subscription</th>
              <th className="text-left p-3">Conn</th>
              <th className="text-left p-3">Router</th>
              <th className="text-left p-3">Static IP</th>
              <th className="text-left p-3">Actions</th>
            </tr>
          </thead>
          <tbody>
            {rows.map((p) => (
              <tr key={p.id} className="border-t">
                <td className="p-3">{p.subscription_no ?? `#${p.subscription_id}`}</td>
                <td className="p-3">{p.device_conn}</td>
                <td className="p-3">{p.router_id}</td>
                <td className="p-3">{p.static_ip ?? "-"}</td>
                <td className="p-3 flex gap-2 flex-wrap">
                  <button
                    onClick={() => pingTest(p.id)}
                    className="px-3 py-1 rounded bg-slate-800 text-white hover:bg-slate-700"
                  >
                    Ping (5s)
                  </button>
                  <Link className="px-3 py-1 rounded bg-slate-200 hover:bg-slate-300" href={`/admin/provisionings/${p.id}/edit`}>
                    Edit
                  </Link>
                  <DangerButton
                    onClick={async () => {
                      await remove(`/provisionings/${p.id}`);
                      await load();
                    }}
                  >
                    Delete
                  </DangerButton>
                </td>
              </tr>
            ))}
          </tbody>
        </table>
      </div>
    </AdminShell>
  );
}