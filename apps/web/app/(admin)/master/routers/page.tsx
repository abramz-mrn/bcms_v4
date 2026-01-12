"use client";

import { useEffect, useMemo, useState } from "react";
import { useMe } from "@/lib/useMe";
import { hasPermission } from "@/lib/permissions";

const STATUSES = ["online","offline","maintenance"] as const;

export default function RoutersPage() {
  const base = process.env.NEXT_PUBLIC_API_BASE_URL || "http://127.0.0.1/api";
  const me = useMe();
  const canManage = hasPermission(me?.group?.permissions, "routers.manage");

  const [search, setSearch] = useState("");
  const [data, setData] = useState<any>(null);
  const [err, setErr] = useState("");

  const [createOpen, setCreateOpen] = useState(false);
  const [edit, setEdit] = useState<any>(null);

  const url = useMemo(() => {
    const u = new URL(`${base}/v1/routers`);
    u.searchParams.set("per_page", "100");
    return u.toString();
  }, [base]);

  async function load() {
    setErr("");
    const r = await fetch(url, { credentials: "include", cache: "no-store" });
    if (!r.ok) { setErr(await r.text()); return; }
    setData(await r.json());
  }

  useEffect(() => { load(); }, [url]);

  const rows = (data?.data ?? []).filter((r: any) => {
    const s = search.trim().toLowerCase();
    if (!s) return true;
    return (
      String(r.name ?? "").toLowerCase().includes(s) ||
      String(r.location ?? "").toLowerCase().includes(s) ||
      String(r.ip_address ?? "").toLowerCase().includes(s)
    );
  });

  return (
    <div className="space-y-4">
      <div className="flex items-end justify-between flex-wrap gap-3">
        <div>
          <h1 className="text-2xl font-semibold">Routers</h1>
          <p className="text-sm text-slate-600">Mikrotik routers / POP.</p>
        </div>
        {canManage && (
          <button className="px-3 py-2 rounded-lg bg-slate-900 text-white text-sm" onClick={() => setCreateOpen(true)}>
            + New Router
          </button>
        )}
      </div>

      {err && (
        <div className="p-4 bg-red-50 border border-red-200 rounded-xl text-sm text-red-700">{err}</div>
      )}

      <div className="bg-white border rounded-2xl p-4 shadow-sm">
        <div className="grid grid-cols-1 md:grid-cols-3 gap-3">
          <div className="md:col-span-2">
            <label className="text-xs text-slate-600">Search</label>
            <input className="mt-1 w-full border rounded-lg p-2 text-sm"
              value={search} onChange={(e)=>setSearch(e.target.value)}
              placeholder="name / location / ip" />
          </div>
          <div className="flex items-end">
            <button className="px-3 py-2 rounded-lg border bg-slate-50 text-sm" onClick={load}>Refresh</button>
          </div>
        </div>
      </div>

      <div className="bg-white border rounded-2xl p-4 shadow-sm overflow-auto">
        <table className="min-w-full text-sm">
          <thead className="bg-slate-50">
            <tr>
              <th className="text-left px-3 py-2 border-b">Name</th>
              <th className="text-left px-3 py-2 border-b">Location</th>
              <th className="text-left px-3 py-2 border-b">IP</th>
              <th className="text-left px-3 py-2 border-b">API Port</th>
              <th className="text-left px-3 py-2 border-b">TLS</th>
              <th className="text-left px-3 py-2 border-b">SSH</th>
              <th className="text-left px-3 py-2 border-b">Status</th>
              <th className="text-left px-3 py-2 border-b">Action</th>
            </tr>
          </thead>
          <tbody>
            {rows.length ? rows.map((r: any) => (
              <tr key={r.id} className="odd:bg-white even:bg-slate-50">
                <td className="px-3 py-2 border-b font-medium">{r.name}</td>
                <td className="px-3 py-2 border-b">{r.location ?? ""}</td>
                <td className="px-3 py-2 border-b">{r.ip_address}</td>
                <td className="px-3 py-2 border-b">{r.api_port}</td>
                <td className="px-3 py-2 border-b">{r.tls_enabled ? "yes" : "no"}</td>
                <td className="px-3 py-2 border-b">{r.ssh_enabled ? `yes:${r.ssh_port}` : "no"}</td>
                <td className="px-3 py-2 border-b">{r.status}</td>
                <td className="px-3 py-2 border-b">
                  {canManage ? (
                    <div className="flex gap-2">
                      <button className="px-3 py-1 rounded-lg border bg-slate-50 hover:bg-slate-100" onClick={() => setEdit(r)}>
                        Edit
                      </button>
                      <DeleteButton url={`${base}/v1/routers/${r.id}`} onDone={load} label="Delete" />
                    </div>
                  ) : (
                    <span className="text-slate-400">—</span>
                  )}
                </td>
              </tr>
            )) : (
              <tr><td colSpan={8} className="px-3 py-6 text-slate-500 text-center">No routers</td></tr>
            )}
          </tbody>
        </table>
      </div>

      {createOpen && (
        <RouterModal
          title="New Router"
          onClose={() => setCreateOpen(false)}
          onSaved={() => { setCreateOpen(false); load(); }}
          save={async (payload) => {
            const r = await fetch(`${base}/v1/routers`, {
              method: "POST",
              credentials: "include",
              headers: { "Content-Type": "application/json" },
              body: JSON.stringify(payload),
            });
            if (!r.ok) throw new Error(await r.text());
          }}
        />
      )}

      {edit && (
        <RouterModal
          title={`Edit Router ${edit.name}`}
          initial={edit}
          onClose={() => setEdit(null)}
          onSaved={() => { setEdit(null); load(); }}
          save={async (payload) => {
            const r = await fetch(`${base}/v1/routers/${edit.id}`, {
              method: "PUT",
              credentials: "include",
              headers: { "Content-Type": "application/json" },
              body: JSON.stringify(payload),
            });
            if (!r.ok) throw new Error(await r.text());
          }}
        />
      )}
    </div>
  );
}

function DeleteButton({ url, onDone, label }: { url: string; onDone: () => void; label: string }) {
  const [loading, setLoading] = useState(false);

  async function del() {
    if (!confirm("Delete item? (soft delete)")) return;
    setLoading(true);
    try {
      const r = await fetch(url, { method: "DELETE", credentials: "include" });
      if (!r.ok) { alert(await r.text()); return; }
      onDone();
    } finally {
      setLoading(false);
    }
  }

  return (
    <button
      className="px-3 py-1 rounded-lg border border-red-200 bg-red-50 hover:bg-red-100 text-red-700 disabled:opacity-60"
      onClick={del}
      disabled={loading}
    >
      {loading ? "..." : label}
    </button>
  );
}

function RouterModal({
  title,
  initial,
  onClose,
  onSaved,
  save,
}: {
  title: string;
  initial?: any;
  onClose: () => void;
  onSaved: () => void;
  save: (payload: any) => Promise<void>;
}) {
  const [err, setErr] = useState("");
  const [loading, setLoading] = useState(false);

  const [form, setForm] = useState({
    name: initial?.name ?? "",
    location: initial?.location ?? "",
    description: initial?.description ?? "",
    ip_address: initial?.ip_address ?? "",
    api_port: String(initial?.api_port ?? 8729),
    ssh_port: String(initial?.ssh_port ?? 22),
    api_username: initial?.api_username ?? "",
    api_password: "", // don't show old secret
    tls_enabled: initial?.tls_enabled ?? true,
    ssh_enabled: initial?.ssh_enabled ?? false,
    status: initial?.status ?? "offline",
  });

  async function submit(e: React.FormEvent) {
    e.preventDefault();
    setErr("");
    setLoading(true);
    try {
      const payload: any = {
        name: form.name,
        location: form.location || null,
        description: form.description || null,
        ip_address: form.ip_address,
        api_port: Number(form.api_port),
        ssh_port: Number(form.ssh_port),
        api_username: form.api_username,
        tls_enabled: !!form.tls_enabled,
        ssh_enabled: !!form.ssh_enabled,
        status: form.status,
      };

      // Only send api_password if user filled it
      if (form.api_password) payload.api_password = form.api_password;

      await save(payload);
      onSaved();
    } catch (e: any) {
      setErr(String(e?.message ?? e));
    } finally {
      setLoading(false);
    }
  }

  return (
    <div className="fixed inset-0 bg-black/30 flex items-center justify-center p-4">
      <div className="w-full max-w-2xl bg-white rounded-2xl border shadow">
        <div className="p-4 border-b flex items-center justify-between">
          <div className="font-semibold">{title}</div>
          <button className="px-3 py-1 rounded-lg border bg-slate-50" onClick={onClose}>Close</button>
        </div>

        <form onSubmit={submit} className="p-4 space-y-4">
          {err && <div className="p-3 bg-red-50 border border-red-200 rounded-xl text-sm text-red-700">{err}</div>}

          <div className="grid grid-cols-1 md:grid-cols-2 gap-3 text-sm">
            <Field label="Name" value={form.name} onChange={(v)=>setForm(f=>({...f, name:v}))} required />
            <Field label="Location" value={form.location} onChange={(v)=>setForm(f=>({...f, location:v}))} />
            <Field label="IP Address" value={form.ip_address} onChange={(v)=>setForm(f=>({...f, ip_address:v}))} required />
            <Field label="API Port (TLS)" value={form.api_port} onChange={(v)=>setForm(f=>({...f, api_port:v}))} required />
            <Field label="SSH Port" value={form.ssh_port} onChange={(v)=>setForm(f=>({...f, ssh_port:v}))} />
            <Field label="API Username" value={form.api_username} onChange={(v)=>setForm(f=>({...f, api_username:v}))} required />
            <Field label="API Password (fill to change)" value={form.api_password} type="password" onChange={(v)=>setForm(f=>({...f, api_password:v}))} />
            <Select label="Status" value={form.status} onChange={(v)=>setForm(f=>({...f, status:v}))} options={STATUSES as any} />
            <label className="flex items-center gap-2 text-sm mt-6">
              <input type="checkbox" checked={form.tls_enabled} onChange={(e)=>setForm(f=>({...f, tls_enabled:e.target.checked}))} />
              TLS API enabled
            </label>
            <label className="flex items-center gap-2 text-sm mt-6">
              <input type="checkbox" checked={form.ssh_enabled} onChange={(e)=>setForm(f=>({...f, ssh_enabled:e.target.checked}))} />
              SSH enabled (fallback)
            </label>
          </div>

          <div>
            <label className="text-xs text-slate-600">Description</label>
            <textarea className="mt-1 w-full border rounded-lg p-2 text-sm" rows={3}
              value={form.description} onChange={(e)=>setForm(f=>({...f, description:e.target.value}))} />
          </div>

          <div className="flex gap-2 justify-end">
            <button className="px-4 py-2 rounded-lg bg-slate-900 text-white text-sm disabled:opacity-60" disabled={loading}>
              {loading ? "Saving..." : "Save"}
            </button>
          </div>

          <div className="text-xs text-slate-500">
            Password tidak ditampilkan. Isi field password hanya jika ingin mengganti.
          </div>
        </form>
      </div>
    </div>
  );
}

function Field({ label, value, onChange, required, type }: { label: string; value: string; onChange:(v:string)=>void; required?: boolean; type?: string }) {
  return (
    <div>
      <label className="text-xs text-slate-600">{label}</label>
      <input type={type ?? "text"} className="mt-1 w-full border rounded-lg p-2 text-sm" value={value} onChange={(e)=>onChange(e.target.value)} required={required} />
    </div>
  );
}

function Select({ label, value, onChange, options }: { label: string; value: string; onChange:(v:string)=>void; options: string[] }) {
  return (
    <div>
      <label className="text-xs text-slate-600">{label}</label>
      <select className="mt-1 w-full border rounded-lg p-2 text-sm" value={value} onChange={(e)=>onChange(e.target.value)}>
        {options.map((o) => <option key={o} value={o}>{o}</option>)}
      </select>
    </div>
  );
}