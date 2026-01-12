"use client";

import { useState } from "react";

export default function NewCustomerPage() {
  const base = process.env.NEXT_PUBLIC_API_BASE_URL || "http://127.0.0.1/api";

  const [form, setForm] = useState({
    code: "",
    name: "",
    phone: "",
    email: "",
    group_area: "",
    address: "",
    city: "",
    state: "",
    pos: "",
    notes: ""
  });
  const [err, setErr] = useState("");
  const [ok, setOk] = useState("");

  async function submit(e: React.FormEvent) {
    e.preventDefault();
    setErr(""); setOk("");

    const r = await fetch(`${base}/v1/customers`, {
      method: "POST",
      credentials: "include",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify(form),
    });

    if (!r.ok) {
      setErr(await r.text());
      return;
    }

    const created = await r.json();
    setOk("Created");
    window.location.href = `/customers/${created.id}`;
  }

  return (
    <div className="max-w-3xl space-y-4">
      <div className="flex items-end justify-between">
        <div>
          <div className="text-sm text-slate-500">
            <a className="underline" href="/customers">Customers</a> / New
          </div>
          <h1 className="text-2xl font-semibold">New Customer</h1>
        </div>
      </div>

      {(err || ok) && (
        <div className={"p-4 rounded-xl border text-sm " + (err ? "bg-red-50 border-red-200 text-red-700" : "bg-green-50 border-green-200 text-green-700")}>
          {err || ok}
        </div>
      )}

      <form onSubmit={submit} className="bg-white border rounded-2xl p-6 shadow-sm space-y-4">
        <div className="grid grid-cols-1 md:grid-cols-2 gap-3">
          <Input label="Code (unique)" value={form.code} onChange={(v)=>setForm(f=>({...f, code:v}))} required />
          <Input label="Name" value={form.name} onChange={(v)=>setForm(f=>({...f, name:v}))} required />
          <Input label="Phone" value={form.phone} onChange={(v)=>setForm(f=>({...f, phone:v}))} />
          <Input label="Email" value={form.email} onChange={(v)=>setForm(f=>({...f, email:v}))} />
          <Input label="Area / Group" value={form.group_area} onChange={(v)=>setForm(f=>({...f, group_area:v}))} />
          <Input label="City" value={form.city} onChange={(v)=>setForm(f=>({...f, city:v}))} />
          <Input label="State" value={form.state} onChange={(v)=>setForm(f=>({...f, state:v}))} />
          <Input label="POS" value={form.pos} onChange={(v)=>setForm(f=>({...f, pos:v}))} />
        </div>
        <Textarea label="Address" value={form.address} onChange={(v)=>setForm(f=>({...f, address:v}))} />
        <Textarea label="Notes" value={form.notes} onChange={(v)=>setForm(f=>({...f, notes:v}))} />

        <div className="flex gap-2">
          <button className="px-4 py-2 rounded-lg bg-slate-900 text-white text-sm">Save</button>
          <a className="px-4 py-2 rounded-lg border bg-slate-50 text-sm" href="/customers">Cancel</a>
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