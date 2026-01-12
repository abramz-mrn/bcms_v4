"use client";

import { useEffect, useMemo, useState } from "react";
import { useMe } from "@/lib/useMe";
import { hasPermission } from "@/lib/permissions";

const CHANNELS = ["email", "whatsapp", "sms"] as const;
const EVENTS = [
  "invoice.reminder.h-3",
  "invoice.reminder.h-1",
  "invoice.reminder.h+1",
  "invoice.reminder.h+3",
] as const;

const DEFAULT_VARS = {
  customer_name: "Budi",
  invoice_number: "INV-202601-000001",
  period_key: "2026-01",
  issue_date: "2026-01-01",
  due_date: "2026-01-08",
  total: "150000",
  days_offset: "-3",
};

export default function TemplatesPage() {
  const base = process.env.NEXT_PUBLIC_API_BASE_URL || "http://127.0.0.1/api";
  const me = useMe();
  const canManage = hasPermission(me?.group?.permissions, "billing.manage");

  const [search, setSearch] = useState("");
  const [channel, setChannel] = useState<string>("");
  const [event, setEvent] = useState<string>("");

  const [data, setData] = useState<any>(null);
  const [err, setErr] = useState("");

  const [createOpen, setCreateOpen] = useState(false);
  const [edit, setEdit] = useState<any>(null);

  const url = useMemo(() => {
    const u = new URL(`${base}/v1/message-templates`);
    u.searchParams.set("per_page", "100");
    if (search.trim()) u.searchParams.set("search", search.trim());
    if (channel) u.searchParams.set("channel", channel);
    if (event) u.searchParams.set("event", event);
    return u.toString();
  }, [base, search, channel, event]);

  async function load() {
    setErr("");
    const r = await fetch(url, { credentials: "include", cache: "no-store" });
    if (!r.ok) { setErr(await r.text()); return; }
    setData(await r.json());
  }

  useEffect(() => { if (canManage) load(); }, [url, canManage]);

  if (!canManage) {
    return (
      <div className="p-4 bg-red-50 border border-red-200 rounded-xl text-sm text-red-700">
        Forbidden: missing permission <code>billing.manage</code>
      </div>
    );
  }

  const rows = data?.data ?? [];

  return (
    <div className="space-y-4">
      <div className="flex items-end justify-between flex-wrap gap-3">
        <div>
          <h1 className="text-2xl font-semibold">Templates</h1>
          <p className="text-sm text-slate-600">Message templates (Email/WhatsApp/SMS) per event.</p>
        </div>
        <button className="px-3 py-2 rounded-lg bg-slate-900 text-white text-sm" onClick={() => setCreateOpen(true)}>
          + New Template
        </button>
      </div>

      {err && <div className="p-4 bg-red-50 border border-red-200 rounded-xl text-sm text-red-700">{err}</div>}

      <div className="bg-white border rounded-2xl p-4 shadow-sm">
        <div className="grid grid-cols-1 md:grid-cols-4 gap-3 text-sm">
          <div className="md:col-span-2">
            <label className="text-xs text-slate-600">Search</label>
            <input className="mt-1 w-full border rounded-lg p-2" value={search} onChange={(e)=>setSearch(e.target.value)} placeholder="key/name/event" />
          </div>
          <div>
            <label className="text-xs text-slate-600">Channel</label>
            <select className="mt-1 w-full border rounded-lg p-2" value={channel} onChange={(e)=>setChannel(e.target.value)}>
              <option value="">All</option>
              {CHANNELS.map(c => <option key={c} value={c}>{c}</option>)}
            </select>
          </div>
          <div>
            <label className="text-xs text-slate-600">Event</label>
            <select className="mt-1 w-full border rounded-lg p-2" value={event} onChange={(e)=>setEvent(e.target.value)}>
              <option value="">All</option>
              {EVENTS.map(ev => <option key={ev} value={ev}>{ev}</option>)}
            </select>
          </div>
        </div>
      </div>

      <div className="bg-white border rounded-2xl p-4 shadow-sm overflow-auto">
        <table className="min-w-full text-sm">
          <thead className="bg-slate-50">
            <tr>
              <th className="text-left px-3 py-2 border-b">Key</th>
              <th className="text-left px-3 py-2 border-b">Name</th>
              <th className="text-left px-3 py-2 border-b">Channel</th>
              <th className="text-left px-3 py-2 border-b">Event</th>
              <th className="text-left px-3 py-2 border-b">Active</th>
              <th className="text-left px-3 py-2 border-b">Action</th>
            </tr>
          </thead>
          <tbody>
            {rows.length ? rows.map((t: any) => (
              <tr key={t.id} className="odd:bg-white even:bg-slate-50">
                <td className="px-3 py-2 border-b font-medium">{t.key}</td>
                <td className="px-3 py-2 border-b">{t.name}</td>
                <td className="px-3 py-2 border-b">{t.channel}</td>
                <td className="px-3 py-2 border-b">{t.event}</td>
                <td className="px-3 py-2 border-b">{t.active ? "yes" : "no"}</td>
                <td className="px-3 py-2 border-b">
                  <div className="flex gap-2">
                    <button className="px-3 py-1 rounded-lg border bg-slate-50 hover:bg-slate-100" onClick={() => setEdit(t)}>Edit</button>
                    <DeleteButton url={`${base}/v1/message-templates/${t.id}`} onDone={load} />
                  </div>
                </td>
              </tr>
            )) : (
              <tr><td colSpan={6} className="px-3 py-6 text-slate-500 text-center">No templates</td></tr>
            )}
          </tbody>
        </table>
      </div>

      {createOpen && (
        <TemplateModal
          title="New Template"
          onClose={() => setCreateOpen(false)}
          onSaved={() => { setCreateOpen(false); load(); }}
          save={async (payload) => {
            const r = await fetch(`${base}/v1/message-templates`, {
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
        <TemplateModal
          title={`Edit Template ${edit.key}`}
          initial={edit}
          onClose={() => setEdit(null)}
          onSaved={() => { setEdit(null); load(); }}
          save={async (payload) => {
            const r = await fetch(`${base}/v1/message-templates/${edit.id}`, {
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
    if (!confirm("Delete template?")) return;
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
    <button className="px-3 py-1 rounded-lg border border-red-200 bg-red-50 hover:bg-red-100 text-red-700 disabled:opacity-60"
      disabled={loading} onClick={del}>
      {loading ? "..." : "Delete"}
    </button>
  );
}

function TemplateModal({
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
  const [err, setErr] = useState("");
  const [loading, setLoading] = useState(false);

  const [form, setForm] = useState({
    key: initial?.key ?? "",
    name: initial?.name ?? "",
    channel: initial?.channel ?? "email",
    event: initial?.event ?? EVENTS[0],
    subject: initial?.subject ?? "Reminder {{invoice_number}}",
    body: initial?.body ?? "Yth {{customer_name}}, invoice {{invoice_number}} total {{total}} jatuh tempo {{due_date}} ({{days_offset}}).",
    active: initial?.active ?? true,
  });

  const [preview, setPreview] = useState<{subject?:string|null, body?:string|null} | null>(null);

  async function doPreview() {
    setErr("");
    const r = await fetch(`${base}/v1/message-templates/preview`, {
      method: "POST",
      credentials: "include",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({ subject: form.subject, body: form.body, vars: DEFAULT_VARS }),
    });
    if (!r.ok) { setErr(await r.text()); return; }
    setPreview(await r.json());
  }

  async function submit(e: React.FormEvent) {
    e.preventDefault();
    setErr("");
    setLoading(true);
    try {
      const payload = {
        key: form.key,
        name: form.name,
        channel: form.channel,
        event: form.event,
        subject: form.channel === "email" ? form.subject : null,
        body: form.body,
        active: !!form.active,
      };
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
      <div className="w-full max-w-3xl bg-white rounded-2xl border shadow">
        <div className="p-4 border-b flex items-center justify-between">
          <div className="font-semibold">{title}</div>
          <button className="px-3 py-1 rounded-lg border bg-slate-50" onClick={onClose}>Close</button>
        </div>

        <form onSubmit={submit} className="p-4 space-y-4 text-sm">
          {err && <div className="p-3 bg-red-50 border border-red-200 rounded-xl text-sm text-red-700">{err}</div>}

          <div className="grid grid-cols-1 md:grid-cols-2 gap-3">
            <Field label="Key (unique)" value={form.key} onChange={(v)=>setForm(f=>({...f, key:v}))} required />
            <Field label="Name" value={form.name} onChange={(v)=>setForm(f=>({...f, name:v}))} required />

            <div>
              <label className="text-xs text-slate-600">Channel</label>
              <select className="mt-1 w-full border rounded-lg p-2" value={form.channel}
                onChange={(e)=>setForm(f=>({...f, channel: e.target.value}))}>
                {CHANNELS.map(c => <option key={c} value={c}>{c}</option>)}
              </select>
            </div>

            <div>
              <label className="text-xs text-slate-600">Event</label>
              <select className="mt-1 w-full border rounded-lg p-2" value={form.event}
                onChange={(e)=>setForm(f=>({...f, event: e.target.value}))}>
                {EVENTS.map(ev => <option key={ev} value={ev}>{ev}</option>)}
              </select>
            </div>

            <label className="flex items-center gap-2 mt-6">
              <input type="checkbox" checked={form.active} onChange={(e)=>setForm(f=>({...f, active: e.target.checked}))} />
              Active
            </label>
          </div>

          {form.channel === "email" && (
            <Field label="Email Subject" value={form.subject} onChange={(v)=>setForm(f=>({...f, subject:v}))} />
          )}

          <div>
            <label className="text-xs text-slate-600">Body</label>
            <textarea className="mt-1 w-full border rounded-lg p-2 font-mono text-xs" rows={10}
              value={form.body} onChange={(e)=>setForm(f=>({...f, body:e.target.value}))} />
            <div className="text-xs text-slate-500 mt-2">
              Variables: {Object.keys(DEFAULT_VARS).map(k => <code key={k} className="mr-2">{{`{{${k}}}`}}</code>)}
            </div>
          </div>

          <div className="flex gap-2 justify-end">
            <button type="button" className="px-3 py-2 rounded-lg border bg-slate-50" onClick={doPreview}>
              Preview
            </button>
            <button className="px-4 py-2 rounded-lg bg-slate-900 text-white disabled:opacity-60" disabled={loading}>
              {loading ? "Saving..." : "Save"}
            </button>
          </div>

          {preview && (
            <div className="bg-slate-50 border rounded-xl p-3">
              <div className="font-semibold mb-2">Preview</div>
              {form.channel === "email" && (
                <div className="mb-2">
                  <div className="text-xs text-slate-600">Subject</div>
                  <div className="font-mono text-xs">{preview.subject}</div>
                </div>
              )}
              <div className="text-xs text-slate-600">Body</div>
              <pre className="whitespace-pre-wrap font-mono text-xs">{preview.body}</pre>
            </div>
          )}
        </form>
      </div>
    </div>
  );
}

function Field({ label, value, onChange, required }: { label: string; value: string; onChange:(v:string)=>void; required?: boolean }) {
  return (
    <div>
      <label className="text-xs text-slate-600">{label}</label>
      <input className="mt-1 w-full border rounded-lg p-2" value={value} onChange={(e)=>onChange(e.target.value)} required={required} />
    </div>
  );
}