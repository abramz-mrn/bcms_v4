"use client";

import { useEffect, useMemo, useState } from "react";
import { useMe } from "@/lib/useMe";
import { hasPermission } from "@/lib/permissions";

export default function InternetServicesPage() {
  const base = process.env.NEXT_PUBLIC_API_BASE_URL || "http://127.0.0.1/api";
  const me = useMe();
  const canManage = hasPermission(me?.group?.permissions, "products.manage");

  const [search, setSearch] = useState("");
  const [data, setData] = useState<any>(null);
  const [err, setErr] = useState("");

  const [createOpen, setCreateOpen] = useState(false);
  const [edit, setEdit] = useState<any>(null);

  const url = useMemo(() => {
    const u = new URL(`${base}/v1/internet-services`);
    u.searchParams.set("per_page", "50");
    if (search.trim()) u.searchParams.set("search", search.trim());
    return u.toString();
  }, [base, search]);

  async function load() {
    setErr("");
    const r = await fetch(url, { credentials: "include", cache: "no-store" });
    if (!r.ok) { setErr(await r.text()); return; }
    setData(await r.json());
  }

  useEffect(() => { load(); }, [url]);

  const rows = data?.data ?? [];

  return (
    <div className="space-y-4">
      <div className="flex items-end justify-between flex-wrap gap-3">
        <div>
          <h1 className="text-2xl font-semibold">Internet Services</h1>
          <p className="text-sm text-slate-600">
            Mapping product ↔ router + policy auto soft-limit/suspend + rate limit.
          </p>
        </div>
        {canManage && (
          <button className="px-3 py-2 rounded-lg bg-slate-900 text-white text-sm" onClick={() => setCreateOpen(true)}>
            + New Internet Service
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
              placeholder="product code/name, router name/location/ip" />
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
              <th className="text-left px-3 py-2 border-b">Product</th>
              <th className="text-left px-3 py-2 border-b">Router</th>
              <th className="text-left px-3 py-2 border-b">Profile</th>
              <th className="text-left px-3 py-2 border-b">Rate</th>
              <th className="text-left px-3 py-2 border-b">Soft-limit</th>
              <th className="text-left px-3 py-2 border-b">Suspend</th>
              <th className="text-left px-3 py-2 border-b">Action</th>
            </tr>
          </thead>
          <tbody>
            {rows.length ? rows.map((it: any) => (
              <tr key={it.id} className="odd:bg-white even:bg-slate-50">
                <td className="px-3 py-2 border-b">
                  <div className="font-medium">{it.product?.code} — {it.product?.name}</div>
                  <div className="text-xs text-slate-500">{it.product?.billing_cycle}</div>
                </td>
                <td className="px-3 py-2 border-b">
                  <div className="font-medium">{it.router?.name}</div>
                  <div className="text-xs text-slate-500">{it.router?.ip_address} {it.router?.location ? `(${it.router.location})` : ""}</div>
                </td>
                <td className="px-3 py-2 border-b">{it.profile ?? ""}</td>
                <td className="px-3 py-2 border-b">
                  <div>{it.rate_limit ?? ""}</div>
                  <div className="text-xs text-slate-500">limit-at: {it.limit_at ?? "-"}</div>
                  <div className="text-xs text-slate-500">prio: {it.priority ?? "-"}</div>
                </td>
                <td className="px-3 py-2 border-b">{it.auto_soft_limit} day(s)</td>
                <td className="px-3 py-2 border-b">{it.auto_suspend} day(s)</td>
                <td className="px-3 py-2 border-b">
                  {canManage ? (
                    <div className="flex gap-2">
                      <button className="px-3 py-1 rounded-lg border bg-slate-50 hover:bg-slate-100"
                        onClick={() => setEdit(it)}>
                        Edit
                      </button>
                      <DeleteButton url={`${base}/v1/internet-services/${it.id}`} onDone={load} />
                    </div>
                  ) : (
                    <span className="text-slate-400">—</span>
                  )}
                </td>
              </tr>
            )) : (
              <tr><td colSpan={7} className="px-3 py-6 text-slate-500 text-center">No internet services</td></tr>
            )}
          </tbody>
        </table>
      </div>

      {createOpen && (
        <InternetServiceModal
          title="New Internet Service"
          onClose={() => setCreateOpen(false)}
          onSaved={() => { setCreateOpen(false); load(); }}
          save={async (payload) => {
            const r = await fetch(`${base}/v1/internet-services`, {
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
        <InternetServiceModal
          title="Edit Internet Service"
          initial={edit}
          onClose={() => setEdit(null)}
          onSaved={() => { setEdit(null); load(); }}
          save={async (payload) => {
            const r = await fetch(`${base}/v1/internet-services/${edit.id}`, {
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

function DeleteButton({ url, onDone }: { url: string; onDone: () => void }) {
  const [loading, setLoading] = useState(false);

  async function del() {
    if (!confirm("Delete internet service? (soft delete)")) return;
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
      {loading ? "..." : "Delete"}
    </button>
  );
}

function InternetServiceModal({
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
  const base = process.env.NEXT_PUBLIC_API_BASE_URL || "http://127.0.0.1/api";
  const [products, setProducts] = useState<any[]>([]);
  const [routers, setRouters] = useState<any[]>([]);
  const [err, setErr] = useState("");
  const [loading, setLoading] = useState(false);

  const [form, setForm] = useState({
    products_id: String(initial?.products_id ?? ""),
    routers_id: String(initial?.routers_id ?? ""),
    profile: initial?.profile ?? "",
    rate_limit: initial?.rate_limit ?? "",
    limit_at: initial?.limit_at ?? "",
    priority: initial?.priority ?? "",
    auto_soft_limit: String(initial?.auto_soft_limit ?? 3),
    auto_suspend: String(initial?.auto_suspend ?? 7),
  });

  useEffect(() => {
    (async () => {
      const pr = await fetch(`${base}/v1/products?per_page=200`, { credentials: "include", cache: "no-store" });
      if (pr.ok) {
        const pj = await pr.json();
        // prefer Internet Services products only
        setProducts((pj.data ?? []).filter((p:any) => p.type === "Internet Services"));
      }
      const rr = await fetch(`${base}/v1/routers?per_page=200`, { credentials: "include", cache: "no-store" });
      if (rr.ok) {
        const rj = await rr.json();
        setRouters(rj.data ?? []);
      }
    })();
  }, [base]);

  async function submit(e: React.FormEvent) {
    e.preventDefault();
    setErr("");
    setLoading(true);
    try {
      await save({
        products_id: Number(form.products_id),
        routers_id: Number(form.routers_id),
        profile: form.profile || null,
        rate_limit: form.rate_limit || null,
        limit_at: form.limit_at || null,
        priority: form.priority || null,
        auto_soft_limit: Number(form.auto_soft_limit),
        auto_suspend: Number(form.auto_suspend),
      });
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
            <div>
              <label className="text-xs text-slate-600">Product (Internet Services)</label>
              <select className="mt-1 w-full border rounded-lg p-2"
                value={form.products_id} onChange={(e)=>setForm(f=>({...f, products_id: e.target.value}))} required>
                <option value="" disabled>Select product</option>
                {products.map((p:any) => <option key={p.id} value={p.id}>{p.code} — {p.name}</option>)}
              </select>
              <div className="text-xs text-slate-500 mt-1">1 product hanya boleh punya 1 internet-service aktif.</div>
            </div>

            <div>
              <label className="text-xs text-slate-600">Router</label>
              <select className="mt-1 w-full border rounded-lg p-2"
                value={form.routers_id} onChange={(e)=>setForm(f=>({...f, routers_id: e.target.value}))} required>
                <option value="" disabled>Select router</option>
                {routers.map((r:any) => <option key={r.id} value={r.id}>{r.name} ({r.ip_address})</option>)}
              </select>
            </div>

            <Field label="Profile (optional)" value={form.profile} onChange={(v)=>setForm(f=>({...f, profile: v}))} />
            <Field label="Rate Limit (e.g. 5M/5M)" value={form.rate_limit} onChange={(v)=>setForm(f=>({...f, rate_limit: v}))} />
            <Field label="Limit At (e.g. 3M/3M)" value={form.limit_at} onChange={(v)=>setForm(f=>({...f, limit_at: v}))} />
            <Field label="Priority (e.g. 8/8)" value={form.priority} onChange={(v)=>setForm(f=>({...f, priority: v}))} />
            <Field label="Auto Soft-limit (days)" value={form.auto_soft_limit} onChange={(v)=>setForm(f=>({...f, auto_soft_limit: v}))} />
            <Field label="Auto Suspend (days)" value={form.auto_suspend} onChange={(v)=>setForm(f=>({...f, auto_suspend: v}))} />
          </div>

          <div className="text-xs text-slate-500">
            Policy rule: <code>auto_suspend</code> sebaiknya &gt;= <code>auto_soft_limit</code> (atau salah satu 0).
          </div>

          <div className="flex gap-2 justify-end">
            <button className="px-4 py-2 rounded-lg bg-slate-900 text-white text-sm disabled:opacity-60" disabled={loading}>
              {loading ? "Saving..." : "Save"}
            </button>
          </div>
        </form>
      </div>
    </div>
  );
}

function Field({ label, value, onChange }: { label: string; value: string; onChange:(v:string)=>void }) {
  return (
    <div>
      <label className="text-xs text-slate-600">{label}</label>
      <input className="mt-1 w-full border rounded-lg p-2 text-sm" value={value} onChange={(e)=>onChange(e.target.value)} />
    </div>
  );
}