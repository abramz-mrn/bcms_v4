"use client";

import { useEffect, useMemo, useState } from "react";

export default function EditCustomerPage({ params }: { params: { id: string } }) {
  const base = process.env.NEXT_PUBLIC_API_BASE_URL || "http://127.0.0.1/api";
  const id = params.id;

  const [form, setForm] = useState<any>(null);
  const [err, setErr] = useState("");
  const [ok, setOk] = useState("");

  const url = useMemo(() => `${base}/v1/customers/${id}`, [base, id]);

  useEffect(() => {
    (async () => {
      setErr("");
      const r = await fetch(url, { credentials: "include", cache: "no-store" });
      if (!r.ok) { setErr(await r.text()); return; }
      const c = await r.json();
      setForm({
        code: c.code ?? "",
        name: c.name ?? "",
        phone: c.phone ?? "",
        email: c.email ?? "",
        group_area: c.group_area ?? "",
        address: c.address ?? "",
        city: c.city ?? "",
        state: c.state ?? "",
        pos: c.pos ?? "",
        notes: c.notes ?? ""
      });
    })();
  }, [url]);

  async function submit(e: React.FormEvent) {
    e.preventDefault();
    setErr(""); setOk("");

    const r = await fetch(`${base}/v1/customers/${id}`, {
      method: "PUT",
      credentials: "include",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify(form),
    });

    if (!r.ok) { setErr(await r.text()); return; }
    setOk("Saved");
    window.location.href = `/customers/${id}`;
  }

  if (!form) {
    return <div className="text-sm text-slate-600">Loading...</div>;
  }

  return (
    <div className="max-w-3xl space-y-4">
      <div>
        <div className="text-sm text-slate-500">
          <a className="underline" href="/customers">Customers</a> / <a className="underline" href={`/customers/${id}`}>{id}</a> / Edit
        </div>
        <h1 className="text-2xl font-semibold">Edit Customer</h1>
      </div>

      {(err || ok) && (
        <div className={"p-4 rounded-xl border text-sm " + (err ? "bg-red-50 border-red-200 text-red-700" : "bg-green-50 border-green-200 text-green-700")}>
          {err || ok}
        </div>
      )}

      <form onSubmit={submit} className="bg-white border rounded-2xl p-6 shadow-sm space-y-4">
        <div className="grid grid-cols-1 md:grid-cols-2 gap-3">
          <Input label="Code (unique)" value={form.code} onChange={(v)=>setForm((f:any)=>({...f, code:v}))} required />
          <Input label="Name" value={form.name} onChange={(v)=>setForm((f:any)=>({...f, name:v}))} required />
          <Input label="Phone" value={form.phone} onChange={(v)=>setForm((f:any)=>({...f, phone:v}))} />
          <Input label="Email" value={form.email} onChange={(v)=>setForm((f:any)=>({...f, email:v}))} />
          <Input label="Area / Group" value={form.group_area} onChange={(v)=>setForm((f:any)=>({...f, group_area:v}))} />
          <Input label="City" value={form.city} onChange={(v)=>setForm((f:any)=>({...f, city:v}))} />
          <Input label="State" value={form.state} onChange={(v)=>setForm((f:any)=>({...f, state:v}))} />
          <Input label="POS" value={form.pos} onChange={(v)=>setForm((f:any)=>({...f, pos:v}))} />
        </div>
        <Textarea label="Address" value={form.address} onChange={(v)=>setForm((f:any)=>({...f, address:v}))} />
        <Textarea label="Notes" value={form.notes} onChange={(v)=>setForm((f:any)=>({...f, notes:v}))} />

        <div className="flex gap-2">
          <button className="px-4 py-2 rounded-lg bg-slate-900 text-white text-sm">Save</button>
          <a className="px-4 py-2 rounded-lg border bg-slate-50 text-sm" href={`/customers/${id}`}>Cancel</a>
        </div>
      </form>
    </div>
  );
}

function Input({ label, value, onChange, required }: { label: string; value: string; onChange: (v:string)=>void; required?: boolean }) {
  return (
    <div>
      <label className="text-xs text-slate-600">{label}</label>
      <input className="mt-1 w-full border rounded-lg p-2 text-sm" value={value} onChange={(e)=>onChange(e.target.value)} required={required} />
    </div>
  );
}
function Textarea({ label, value, onChange }: { label: string; value: string; onChange: (v:string)=>void }) {
  return (
    <div>
      <label className="text-xs text-slate-600">{label}</label>
      <textarea className="mt-1 w-full border rounded-lg p-2 text-sm" rows={3} value={value} onChange={(e)=>onChange(e.target.value)} />
    </div>
  );
}