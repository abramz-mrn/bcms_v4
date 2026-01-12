"use client";

import { useEffect, useMemo, useState } from "react";
import { useMe } from "@/lib/useMe";
import { hasPermission } from "@/lib/permissions";

const TYPES = ["Internet Services","Additional Services","Other Services","Equipments"] as const;
const SEGMENTS = ["Residensial","SOHO/UMKM","Corporate","Others"] as const;
const CYCLES = ["One time charge","Weekly","Monthly","Quarterly","Semi-annually","Annually"] as const;

export default function ProductsPage() {
  const base = process.env.NEXT_PUBLIC_API_BASE_URL || "http://127.0.0.1/api";
  const me = useMe();
  const canManage = hasPermission(me?.group?.permissions, "products.manage");

  const [search, setSearch] = useState("");
  const [data, setData] = useState<any>(null);
  const [err, setErr] = useState("");

  const [createOpen, setCreateOpen] = useState(false);
  const [edit, setEdit] = useState<any>(null);

  const url = useMemo(() => {
    const u = new URL(`${base}/v1/products`);
    u.searchParams.set("per_page", "100");
    // API products controller belum ada search; kita filter client-side.
    return u.toString();
  }, [base]);

  async function load() {
    setErr("");
    const r = await fetch(url, { credentials: "include", cache: "no-store" });
    if (!r.ok) { setErr(await r.text()); return; }
    setData(await r.json());
  }

  useEffect(() => { load(); }, [url]);

  const rows = (data?.data ?? []).filter((p: any) => {
    const s = search.trim().toLowerCase();
    if (!s) return true;
    return (
      String(p.code ?? "").toLowerCase().includes(s) ||
      String(p.name ?? "").toLowerCase().includes(s) ||
      String(p.type ?? "").toLowerCase().includes(s)
    );
  });

  return (
    <div className="space-y-4">
      <div className="flex items-end justify-between flex-wrap gap-3">
        <div>
          <h1 className="text-2xl font-semibold">Products</h1>
          <p className="text-sm text-slate-600">Master data products & services.</p>
        </div>
        {canManage && (
          <button className="px-3 py-2 rounded-lg bg-slate-900 text-white text-sm" onClick={() => setCreateOpen(true)}>
            + New Product
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
              placeholder="code / name / type" />
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
              <th className="text-left px-3 py-2 border-b">Code</th>
              <th className="text-left px-3 py-2 border-b">Name</th>
              <th className="text-left px-3 py-2 border-b">Type</th>
              <th className="text-left px-3 py-2 border-b">Billing</th>
              <th className="text-left px-3 py-2 border-b">Price</th>
              <th className="text-left px-3 py-2 border-b">Tax</th>
              <th className="text-left px-3 py-2 border-b">Action</th>
            </tr>
          </thead>
          <tbody>
            {rows.length ? rows.map((p: any) => (
              <tr key={p.id} className="odd:bg-white even:bg-slate-50">
                <td className="px-3 py-2 border-b font-medium">{p.code}</td>
                <td className="px-3 py-2 border-b">{p.name}</td>
                <td className="px-3 py-2 border-b">{p.type}</td>
                <td className="px-3 py-2 border-b">{p.billing_cycle}</td>
                <td className="px-3 py-2 border-b">{p.price}</td>
                <td className="px-3 py-2 border-b">{p.tax_rate}% {p.tax_included ? "(incl)" : ""}</td>
                <td className="px-3 py-2 border-b">
                  {canManage ? (
                    <div className="flex gap-2">
                      <button className="px-3 py-1 rounded-lg border bg-slate-50 hover:bg-slate-100"
                        onClick={() => setEdit(p)}>
                        Edit
                      </button>
                      <DeleteButton url={`${base}/v1/products/${p.id}`} onDone={load} label="Delete" />
                    </div>
                  ) : (
                    <span className="text-slate-400">—</span>
                  )}
                </td>
              </tr>
            )) : (
              <tr><td colSpan={7} className="px-3 py-6 text-slate-500 text-center">No products</td></tr>
            )}
          </tbody>
        </table>
      </div>

      {createOpen && (
        <ProductModal
          title="New Product"
          onClose={() => setCreateOpen(false)}
          onSaved={() => { setCreateOpen(false); load(); }}
          save={async (payload) => {
            const r = await fetch(`${base}/v1/products`, {
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
        <ProductModal
          title={`Edit Product ${edit.code}`}
          initial={edit}
          onClose={() => setEdit(null)}
          onSaved={() => { setEdit(null); load(); }}
          save={async (payload) => {
            const r = await fetch(`${base}/v1/products/${edit.id}`, {
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

function ProductModal({
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
    code: initial?.code ?? "",
    name: initial?.name ?? "",
    type: initial?.type ?? TYPES[0],
    description: initial?.description ?? "",
    market_segment: initial?.market_segment ?? SEGMENTS[0],
    billing_cycle: initial?.billing_cycle ?? CYCLES[2],
    price: String(initial?.price ?? "0"),
    tax_rate: String(initial?.tax_rate ?? "11"),
    tax_included: !!initial?.tax_included,
  });

  async function submit(e: React.FormEvent) {
    e.preventDefault();
    setErr("");
    setLoading(true);
    try {
      await save({
        ...form,
        price: Number(form.price),
        tax_rate: Number(form.tax_rate),
        tax_included: !!form.tax_included,
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
            <Field label="Code" value={form.code} onChange={(v)=>setForm(f=>({...f, code:v}))} required />
            <Field label="Name" value={form.name} onChange={(v)=>setForm(f=>({...f, name:v}))} required />
            <Select label="Type" value={form.type} onChange={(v)=>setForm(f=>({...f, type:v}))} options={TYPES as any} />
            <Select label="Market Segment" value={form.market_segment} onChange={(v)=>setForm(f=>({...f, market_segment:v}))} options={SEGMENTS as any} />
            <Select label="Billing Cycle" value={form.billing_cycle} onChange={(v)=>setForm(f=>({...f, billing_cycle:v}))} options={CYCLES as any} />
            <Field label="Price" value={form.price} onChange={(v)=>setForm(f=>({...f, price:v}))} required />
            <Field label="Tax Rate (%)" value={form.tax_rate} onChange={(v)=>setForm(f=>({...f, tax_rate:v}))} required />
            <label className="flex items-center gap-2 text-sm mt-6">
              <input type="checkbox" checked={form.tax_included} onChange={(e)=>setForm(f=>({...f, tax_included:e.target.checked}))} />
              Tax included
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
        </form>
      </div>
    </div>
  );
}

function Field({ label, value, onChange, required }: { label: string; value: string; onChange:(v:string)=>void; required?: boolean }) {
  return (
    <div>
      <label className="text-xs text-slate-600">{label}</label>
      <input className="mt-1 w-full border rounded-lg p-2 text-sm" value={value} onChange={(e)=>onChange(e.target.value)} required={required} />
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