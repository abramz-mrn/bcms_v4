"use client";

import { useEffect, useMemo, useState } from "react";

const STATUSES = ["Registered","Active","Soft-Limit","Suspend","Terminated"] as const;

export default function EditSubscriptionPage({
  params,
}: {
  params: { id: string; subscriptionId: string };
}) {
  const base = process.env.NEXT_PUBLIC_API_BASE_URL || "http://127.0.0.1/api";
  const customerId = params.id;
  const subscriptionId = params.subscriptionId;

  const [products, setProducts] = useState<any[]>([]);
  const [form, setForm] = useState<any>(null);
  const [err, setErr] = useState("");
  const [ok, setOk] = useState("");

  const subUrl = useMemo(() => `${base}/v1/subscriptions/${subscriptionId}`, [base, subscriptionId]);

  useEffect(() => {
    (async () => {
      setErr("");
      // load products for select
      const pr = await fetch(`${base}/v1/products?per_page=100`, { credentials: "include", cache: "no-store" });
      if (pr.ok) {
        const pj = await pr.json();
        setProducts(pj.data ?? []);
      }

      // load subscription
      const r = await fetch(subUrl, { credentials: "include", cache: "no-store" });
      if (!r.ok) { setErr(await r.text()); return; }
      const s = await r.json();

      setForm({
        customers_id: s.customers_id,
        products_id: String(s.products_id ?? ""),
        registration_date: (s.registration_date ?? "").slice(0, 10),
        status: s.status ?? "Registered",
        email_consent: !!s.email_consent,
        sms_consent: !!s.sms_consent,
        whatsapp_consent: !!s.whatsapp_consent,
      });
    })();
  }, [base, subUrl]);

  async function submit(e: React.FormEvent) {
    e.preventDefault();
    setErr(""); setOk("");

    const payload = {
      products_id: Number(form.products_id),
      registration_date: form.registration_date,
      status: form.status,
      email_consent: form.email_consent,
      sms_consent: form.sms_consent,
      whatsapp_consent: form.whatsapp_consent,
    };

    const r = await fetch(`${base}/v1/subscriptions/${subscriptionId}`, {
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
    <div className="max-w-3xl space-y-4">
      <div>
        <div className="text-sm text-slate-500">
          <a className="underline" href={`/customers/${customerId}`}>Customer</a>
          {" / "}
          Subscription #{subscriptionId} / Edit
        </div>
        <h1 className="text-2xl font-semibold">Edit Subscription</h1>
      </div>

      {(err || ok) && (
        <div className={"p-4 rounded-xl border text-sm " + (err ? "bg-red-50 border-red-200 text-red-700" : "bg-green-50 border-green-200 text-green-700")}>
          {err || ok}
        </div>
      )}

      <form onSubmit={submit} className="bg-white border rounded-2xl p-6 shadow-sm space-y-4">
        <div className="grid grid-cols-1 md:grid-cols-3 gap-3 text-sm">
          <div>
            <div className="text-xs text-slate-600">Product</div>
            <select
              className="mt-1 w-full border rounded-lg p-2"
              value={form.products_id}
              onChange={(e) => setForm((f:any)=>({ ...f, products_id: e.target.value }))}
              required
            >
              <option value="" disabled>Select product</option>
              {products.map((p) => (
                <option key={p.id} value={p.id}>{p.code} — {p.name}</option>
              ))}
            </select>
          </div>

          <div>
            <div className="text-xs text-slate-600">Registration Date</div>
            <input
              className="mt-1 w-full border rounded-lg p-2"
              value={form.registration_date}
              onChange={(e) => setForm((f:any)=>({ ...f, registration_date: e.target.value }))}
              required
            />
          </div>

          <div>
            <div className="text-xs text-slate-600">Status</div>
            <select
              className="mt-1 w-full border rounded-lg p-2"
              value={form.status}
              onChange={(e) => setForm((f:any)=>({ ...f, status: e.target.value }))}
            >
              {STATUSES.map((s) => <option key={s} value={s}>{s}</option>)}
            </select>
          </div>
        </div>

        <div className="flex flex-wrap gap-4 text-sm">
          <label className="flex items-center gap-2">
            <input type="checkbox" checked={form.email_consent}
              onChange={(e)=>setForm((f:any)=>({ ...f, email_consent: e.target.checked }))} />
            Email consent
          </label>
          <label className="flex items-center gap-2">
            <input type="checkbox" checked={form.sms_consent}
              onChange={(e)=>setForm((f:any)=>({ ...f, sms_consent: e.target.checked }))} />
            SMS consent
          </label>
          <label className="flex items-center gap-2">
            <input type="checkbox" checked={form.whatsapp_consent}
              onChange={(e)=>setForm((f:any)=>({ ...f, whatsapp_consent: e.target.checked }))} />
            WhatsApp consent
          </label>
        </div>

        <div className="flex gap-2">
          <button className="px-4 py-2 rounded-lg bg-slate-900 text-white text-sm">Save</button>
          <a className="px-4 py-2 rounded-lg border bg-slate-50 text-sm" href={`/customers/${customerId}`}>Cancel</a>
        </div>

        <div className="text-xs text-slate-500">
          Note: aturan validasi mencegah duplikasi status Active/Soft-Limit/Suspend untuk product yang sama pada customer yang sama.
        </div>
      </form>
    </div>
  );
}