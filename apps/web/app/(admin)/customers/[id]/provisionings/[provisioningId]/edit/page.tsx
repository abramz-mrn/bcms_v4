"use client";

import { useEffect, useMemo, useState } from "react";

export default function EditProvisioningPage({
  params,
}: {
  params: { id: string; provisioningId: string };
}) {
  const base = process.env.NEXT_PUBLIC_API_BASE_URL || "http://127.0.0.1/api";
  const customerId = params.id;
  const provisioningId = params.provisioningId;

  const [routers, setRouters] = useState<any[]>([]);
  const [subs, setSubs] = useState<any[]>([]);
  const [form, setForm] = useState<any>(null);
  const [err, setErr] = useState("");
  const [ok, setOk] = useState("");

  const provUrl = useMemo(() => `${base}/v1/provisionings/${provisioningId}`, [base, provisioningId]);

  useEffect(() => {
    (async () => {
      setErr("");

      // Load routers for dropdown
      const rr = await fetch(`${base}/v1/routers?per_page=100`, { credentials: "include", cache: "no-store" });
      if (rr.ok) {
        const rj = await rr.json();
        setRouters(rj.data ?? []);
      }

      // Load customer to get subscriptions for dropdown (so subscription can be re-bound if needed)
      const cr = await fetch(`${base}/v1/customers/${customerId}`, { credentials: "include", cache: "no-store" });
      if (cr.ok) {
        const cj = await cr.json();
        setSubs(cj.subscriptions ?? []);
      }

      // Load provisioning
      const r = await fetch(provUrl, { credentials: "include", cache: "no-store" });
      if (!r.ok) { setErr(await r.text()); return; }
      const p = await r.json();

      setForm({
        subscriptions_id: String(p.subscriptions_id ?? ""),
        routers_id: String(p.routers_id ?? ""),
        device_conn: p.device_conn ?? "PPPoE",

        device_brand: p.device_brand ?? "",
        device_type_device_sn: p.device_type_device_sn ?? "",
        device_mac: p.device_mac ?? "",

        pppoe_name: p.pppoe_name ?? "",
        pppoe_password: "", // do not show existing secrets
        static_ip: p.static_ip ?? "",
        static_gateway: p.static_gateway ?? "",
      });
    })();
  }, [base, customerId, provUrl]);

  async function submit(e: React.FormEvent) {
    e.preventDefault();
    setErr(""); setOk("");

    const payload: any = {
      subscriptions_id: Number(form.subscriptions_id),
      routers_id: Number(form.routers_id),
      device_conn: form.device_conn,

      device_brand: form.device_brand || null,
      device_type_device_sn: form.device_type_device_sn || null,
      device_mac: form.device_mac || null,
    };

    if (form.device_conn === "PPPoE") {
      payload.pppoe_name = form.pppoe_name;
      // only send password if user filled it
      if (form.pppoe_password) payload.pppoe_password = form.pppoe_password;
      // ensure static fields cleared server-side too
      payload.static_ip = null;
      payload.static_gateway = null;
    } else {
      payload.static_ip = form.static_ip;
      payload.static_gateway = form.static_gateway || null;
      payload.pppoe_name = null;
      payload.pppoe_password = null;
    }

    const r = await fetch(`${base}/v1/provisionings/${provisioningId}`, {
      method: "PUT",
      credentials: "include",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify(payload),
    });

    if (!r.ok) {
      setErr(await r.text());
      return;
    }

    setOk("Saved");
    window.location.href = `/customers/${customerId}`;
  }

  if (!form) return <div className="text-sm text-slate-600">Loading...</div>;

  return (
    <div className="max-w-4xl space-y-4">
      <div>
        <div className="text-sm text-slate-500">
          <a className="underline" href={`/customers/${customerId}`}>Customer</a>
          {" / "}
          Provisioning #{provisioningId} / Edit
        </div>
        <h1 className="text-2xl font-semibold">Edit Provisioning</h1>
      </div>

      {(err || ok) && (
        <div className={"p-4 rounded-xl border text-sm " + (err ? "bg-red-50 border-red-200 text-red-700" : "bg-green-50 border-green-200 text-green-700")}>
          {err || ok}
        </div>
      )}

      <form onSubmit={submit} className="bg-white border rounded-2xl p-6 shadow-sm space-y-4">
        <div className="grid grid-cols-1 md:grid-cols-3 gap-3 text-sm">
          <div>
            <div className="text-xs text-slate-600">Subscription</div>
            <select
              className="mt-1 w-full border rounded-lg p-2"
              value={form.subscriptions_id}
              onChange={(e)=>setForm((f:any)=>({ ...f, subscriptions_id: e.target.value }))}
              required
            >
              <option value="" disabled>Select subscription</option>
              {subs.map((s:any) => (
                <option key={s.id} value={s.id}>#{s.id} — {s?.product?.name ?? "Product"}</option>
              ))}
            </select>
          </div>

          <div>
            <div className="text-xs text-slate-600">Router</div>
            <select
              className="mt-1 w-full border rounded-lg p-2"
              value={form.routers_id}
              onChange={(e)=>setForm((f:any)=>({ ...f, routers_id: e.target.value }))}
              required
            >
              <option value="" disabled>Select router</option>
              {routers.map((r:any) => (
                <option key={r.id} value={r.id}>{r.name} ({r.ip_address})</option>
              ))}
            </select>
          </div>

          <div>
            <div className="text-xs text-slate-600">Connection</div>
            <select
              className="mt-1 w-full border rounded-lg p-2"
              value={form.device_conn}
              onChange={(e)=>setForm((f:any)=>({ ...f, device_conn: e.target.value }))}
            >
              <option value="PPPoE">PPPoE</option>
              <option value="Static-IP">Static-IP</option>
            </select>
          </div>
        </div>

        <div className="grid grid-cols-1 md:grid-cols-3 gap-3 text-sm">
          <FieldInput label="Device Brand" value={form.device_brand} onChange={(v)=>setForm((f:any)=>({ ...f, device_brand: v }))} />
          <FieldInput label="Device Type / SN" value={form.device_type_device_sn} onChange={(v)=>setForm((f:any)=>({ ...f, device_type_device_sn: v }))} />
          <FieldInput label="Device MAC" placeholder="AA:BB:CC:DD:EE:FF" value={form.device_mac} onChange={(v)=>setForm((f:any)=>({ ...f, device_mac: v }))} />
        </div>

        {form.device_conn === "PPPoE" ? (
          <div className="grid grid-cols-1 md:grid-cols-2 gap-3 text-sm">
            <FieldInput label="PPPoE Name" value={form.pppoe_name} onChange={(v)=>setForm((f:any)=>({ ...f, pppoe_name: v }))} required />
            <FieldInput label="PPPoE Password (fill to change)" type="password" value={form.pppoe_password} onChange={(v)=>setForm((f:any)=>({ ...f, pppoe_password: v }))} />
          </div>
        ) : (
          <div className="grid grid-cols-1 md:grid-cols-2 gap-3 text-sm">
            <FieldInput label="Static IP" value={form.static_ip} onChange={(v)=>setForm((f:any)=>({ ...f, static_ip: v }))} required />
            <FieldInput label="Gateway (optional)" value={form.static_gateway} onChange={(v)=>setForm((f:any)=>({ ...f, static_gateway: v }))} />
          </div>
        )}

        <div className="flex gap-2">
          <button className="px-4 py-2 rounded-lg bg-slate-900 text-white text-sm">Save</button>
          <a className="px-4 py-2 rounded-lg border bg-slate-50 text-sm" href={`/customers/${customerId}`}>Cancel</a>
        </div>

        <div className="text-xs text-slate-500">
          Note: PPPoE name & Static IP uniqueness enforced per router; MAC uniqueness enforced globally (non-deleted).
        </div>
      </form>
    </div>
  );
}

function FieldInput({
  label, value, onChange, required, type, placeholder
}: {
  label: string;
  value: string;
  onChange: (v:string)=>void;
  required?: boolean;
  type?: string;
  placeholder?: string;
}) {
  return (
    <div>
      <div className="text-xs text-slate-600">{label}</div>
      <input
        type={type ?? "text"}
        placeholder={placeholder}
        className="mt-1 w-full border rounded-lg p-2"
        value={value}
        onChange={(e)=>onChange(e.target.value)}
        required={required}
      />
    </div>
  );
}