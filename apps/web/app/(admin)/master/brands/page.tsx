"use client";

import { useEffect, useMemo, useState } from "react";
import { useMe } from "@/lib/useMe";
import { hasPermission } from "@/lib/permissions";

export default function BrandsPage() {
  const base = process.env.NEXT_PUBLIC_API_BASE_URL || "http://127.0.0.1/api";
  const me = useMe();
  const canManage = hasPermission(me?.group?.permissions, "brands.manage");

  const [companies, setCompanies] = useState<any[]>([]);
  const [search, setSearch] = useState("");
  const [data, setData] = useState<any>(null);
  const [err, setErr] = useState("");

  const [createOpen, setCreateOpen] = useState(false);
  const [edit, setEdit] = useState<any>(null);

  const url = useMemo(() => {
    const u = new URL(`${base}/v1/brands`);
    u.searchParams.set("per_page", "100");
    return u.toString();
  }, [base]);

  useEffect(() => {
    (async () => {
      const r = await fetch(`${base}/v1/companies?per_page=100`, { credentials: "include", cache: "no-store" });
      if (r.ok) {
        const j = await r.json();
        setCompanies(j.data ?? []);
      }
    })();
  }, [base]);

  async function load() {
    setErr("");
    const r = await fetch(url, { credentials: "include", cache: "no-store" });
    if (!r.ok) { setErr(await r.text()); return; }
    setData(await r.json());
  }

  useEffect(() => { load(); }, [url]);

  const rows = (data?.data ?? []).filter((b: any) => {
    const s = search.trim().toLowerCase();
    if (!s) return true;
    return String(b.name ?? "").toLowerCase().includes(s);
  });

  return (
    <div className="space-y-4">
      <div className="flex items-end justify-between flex-wrap gap-3">
        <div>
          <h1 className="text-2xl font-semibold">Brands</h1>
          <p className="text-sm text-slate-600">Master data brands.</p>
        </div>
        {canManage && (
          <button className="px-3 py-2 rounded-lg bg-slate-900 text-white text-sm" onClick={() => setCreateOpen(true)}>
            + New Brand
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
              placeholder="brand name" />
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
              <th className="text-left px-3 py-2 border-b">ID</th>
              <th className="text-left px-3 py-2 border-b">Company</th>
              <th className="text-left px-3 py-2 border-b">Name</th>
              <th className="text-left px-3 py-2 border-b">Description</th>
              <th className="text-left px-3 py-2 border-b">Action</th>
            </tr>
          </thead>
          <tbody>
            {rows.length ? rows.map((b: any) => (
              <tr key={b.id} className="odd:bg-white even:bg-slate-50">
                <td className="px-3 py-2 border-b">{b.id}</td>
                <td className="px-3 py-2 border-b">{b.companies_id}</td>
                <td className="px-3 py-2 border-b font-medium">{b.name}</td>
                <td className="px-3 py-2 border-b">{b.description ?? ""}</td>
                <td className="px-3 py-2 border-b">
                  {canManage ? (
                    <div className="flex gap-2">
                      <button className="px-3 py-1 rounded-lg border bg-slate-50 hover:bg-slate-100" onClick={() => setEdit(b)}>
                        Edit
                      </button>
                      <DeleteButton url={`${base}/v1/brands/${b.id}`} onDone={load} label="Delete" />
                    </div>
                  ) : (
                    <span className="text-slate-400">—</span>
                  )}
                </td>
              </tr>
            )) : (
              <tr><td colSpan={5} className="px-3 py-6 text-slate-500 text-center">No brands</td></tr>
            )}
          </tbody>
        </table>
      </div>

      {createOpen && (
        <BrandModal
          title="New Brand"
          companies={companies}
          onClose={() => setCreateOpen(false)}
          onSaved={() => { setCreateOpen(false); load(); }}
          save={async (payload) => {
            const r = await fetch(`${base}/v1/brands`, {
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
        <BrandModal
          title={`Edit Brand ${edit.name}`}
          initial={edit}
          companies={companies}
          onClose={() => setEdit(null)}
          onSaved={() => { setEdit(null); load(); }}
          save={async (payload) => {
            const r = await fetch(`${base}/v1/brands/${edit.id}`, {
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

function BrandModal({
  title,
  initial,
  companies,
  onClose,
  onSaved,
  save,
}: {
  title: string;
  initial?: any;
  companies: any[];
  onClose: () => void;
  onSaved: () => void;
  save: (payload: any) => Promise<void>;
}) {
  const [err, setErr] = useState("");
  const [loading, setLoading] = useState(false);

  const [form, setForm] = useState({
    companies_id: String(initial?.companies_id ?? (companies?.[0]?.id ?? "")),
    name: initial?.name ?? "",
    description: initial?.description ?? "",
  });

  async function submit(e: React.FormEvent) {
    e.preventDefault();
    setErr("");
    setLoading(true);
    try {
      await save({
        companies_id: Number(form.companies_id),
        name: form.name,
        description: form.description || null,
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
      <div className="w-full max-w-xl bg-white rounded-2xl border shadow">
        <div className="p-4 border-b flex items-center justify-between">
          <div className="font-semibold">{title}</div>
          <button className="px-3 py-1 rounded-lg border bg-slate-50" onClick={onClose}>Close</button>
        </div>

        <form onSubmit={submit} className="p-4 space-y-4">
          {err && <div className="p-3 bg-red-50 border border-red-200 rounded-xl text-sm text-red-700">{err}</div>}

          <div className="grid grid-cols-1 gap-3 text-sm">
            <div>
              <label className="text-xs text-slate-600">Company</label>
              <select className="mt-1 w-full border rounded-lg p-2" value={form.companies_id}
                onChange={(e)=>setForm(f=>({...f, companies_id: e.target.value}))}>
                {companies.map((c:any) => <option key={c.id} value={c.id}>{c.name}</option>)}
              </select>
            </div>

            <div>
              <label className="text-xs text-slate-600">Name</label>
              <input className="mt-1 w-full border rounded-lg p-2" value={form.name}
                onChange={(e)=>setForm(f=>({...f, name: e.target.value}))} required />
            </div>

            <div>
              <label className="text-xs text-slate-600">Description</label>
              <textarea className="mt-1 w-full border rounded-lg p-2" rows={3} value={form.description}
                onChange={(e)=>setForm(f=>({...f, description: e.target.value}))} />
            </div>
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