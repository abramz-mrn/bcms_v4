"use client";

import { useEffect, useMemo, useState } from "react";

type AuditLog = {
  id: number;
  users_name: string | null;
  ip_address: string | null;
  action: string;
  resource_type: string | null;
  description: string | null;
  created_at: string;
};

export default function AuditLogsPage() {
  const base = process.env.NEXT_PUBLIC_API_BASE_URL || "http://127.0.0.1/api";

  const [action, setAction] = useState("");
  const [resourceType, setResourceType] = useState("");
  const [userName, setUserName] = useState("");
  const [from, setFrom] = useState("");
  const [to, setTo] = useState("");

  const [data, setData] = useState<any>(null);
  const [err, setErr] = useState("");

  const url = useMemo(() => {
    const u = new URL(`${base}/v1/audit-logs`);
    if (action) u.searchParams.set("action", action);
    if (resourceType) u.searchParams.set("resource_type", resourceType);
    if (userName) u.searchParams.set("user_name", userName);
    if (from) u.searchParams.set("from", from);
    if (to) u.searchParams.set("to", to);
    return u.toString();
  }, [base, action, resourceType, userName, from, to]);

  async function load() {
    setErr("");
    const r = await fetch(url, { credentials: "include", cache: "no-store" });
    if (!r.ok) {
      setErr(`${r.status} ${r.statusText}: ${await r.text()}`);
      return;
    }
    setData(await r.json());
  }

  useEffect(() => {
    load();
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, [url]);

  const logs: AuditLog[] = data?.data ?? [];

  return (
    <div className="space-y-4">
      <div>
        <h1 className="text-2xl font-semibold">Audit Logs</h1>
        <p className="text-sm text-slate-600">Jejak aksi user (create/update/delete/login/logout).</p>
      </div>

      <div className="bg-white border rounded-2xl p-4 shadow-sm">
        <div className="grid grid-cols-1 md:grid-cols-6 gap-3">
          <div>
            <label className="text-xs text-slate-600">Action</label>
            <input className="mt-1 w-full border rounded-lg p-2 text-sm"
              placeholder="create/update/delete/login"
              value={action} onChange={(e)=>setAction(e.target.value)} />
          </div>
          <div className="md:col-span-2">
            <label className="text-xs text-slate-600">Resource type (contains)</label>
            <input className="mt-1 w-full border rounded-lg p-2 text-sm"
              placeholder="api/v1/customers"
              value={resourceType} onChange={(e)=>setResourceType(e.target.value)} />
          </div>
          <div>
            <label className="text-xs text-slate-600">User name</label>
            <input className="mt-1 w-full border rounded-lg p-2 text-sm"
              placeholder="Abramz"
              value={userName} onChange={(e)=>setUserName(e.target.value)} />
          </div>
          <div>
            <label className="text-xs text-slate-600">From (YYYY-MM-DD)</label>
            <input className="mt-1 w-full border rounded-lg p-2 text-sm"
              value={from} onChange={(e)=>setFrom(e.target.value)} />
          </div>
          <div>
            <label className="text-xs text-slate-600">To (YYYY-MM-DD)</label>
            <input className="mt-1 w-full border rounded-lg p-2 text-sm"
              value={to} onChange={(e)=>setTo(e.target.value)} />
          </div>
        </div>

        <div className="mt-3 flex gap-2">
          <button
            className="px-3 py-2 rounded-lg bg-slate-900 text-white text-sm"
            onClick={load}
          >
            Refresh
          </button>
          <button
            className="px-3 py-2 rounded-lg border bg-slate-50 text-sm"
            onClick={() => { setAction(""); setResourceType(""); setUserName(""); setFrom(""); setTo(""); }}
          >
            Reset
          </button>
        </div>
      </div>

      {err && (
        <div className="p-4 bg-red-50 border border-red-200 rounded-xl text-sm text-red-700">
          {err}
        </div>
      )}

      <div className="bg-white border rounded-2xl p-4 shadow-sm overflow-auto">
        <table className="min-w-full text-sm">
          <thead className="bg-slate-50">
            <tr>
              <th className="text-left px-3 py-2 border-b">Time</th>
              <th className="text-left px-3 py-2 border-b">User</th>
              <th className="text-left px-3 py-2 border-b">IP</th>
              <th className="text-left px-3 py-2 border-b">Action</th>
              <th className="text-left px-3 py-2 border-b">Resource</th>
              <th className="text-left px-3 py-2 border-b">Description</th>
            </tr>
          </thead>
          <tbody>
            {logs.length ? logs.map((l) => (
              <tr key={l.id} className="odd:bg-white even:bg-slate-50">
                <td className="px-3 py-2 border-b whitespace-nowrap">{l.created_at}</td>
                <td className="px-3 py-2 border-b">{l.users_name ?? ""}</td>
                <td className="px-3 py-2 border-b">{l.ip_address ?? ""}</td>
                <td className="px-3 py-2 border-b font-medium">{l.action}</td>
                <td className="px-3 py-2 border-b">{l.resource_type ?? ""}</td>
                <td className="px-3 py-2 border-b">{l.description ?? ""}</td>
              </tr>
            )) : (
              <tr>
                <td colSpan={6} className="px-3 py-6 text-slate-500 text-center">
                  No logs
                </td>
              </tr>
            )}
          </tbody>
        </table>

        {data && (
          <div className="mt-3 text-xs text-slate-500">
            Page {data.current_page} / {data.last_page} — Total {data.total}
          </div>
        )}
      </div>
    </div>
  );
}