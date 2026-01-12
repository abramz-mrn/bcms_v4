"use client";

import AdminShell from "@/components/AdminShell";
import { Card, Field } from "@/components/ui";
import { CrudMessage, PrimaryButton, useCrudSubmit } from "@/components/crud";
import { apiGet } from "@/lib/api";
import { useEffect, useState } from "react";

export default function EditTemplatePage({ params }: { params: { id: string } }) {
  const id = params.id;
  const { msg, setMsg, update } = useCrudSubmit();
  const [row, setRow] = useState<any | null>(null);
  const [content, setContent] = useState("");

  useEffect(() => {
    apiGet(`/templates/${id}`, { credentials: "include" })
      .then((r) => {
        setRow(r);
        setContent(r.content ?? "");
      })
      .catch((e) => setMsg(e.message));
  }, [id, setMsg]);

  async function onSubmit(e: React.FormEvent<HTMLFormElement>) {
    e.preventDefault();
    setMsg("");

    const form = new FormData(e.currentTarget);
    const payload: any = Object.fromEntries(form.entries());
    payload.content = content;
    payload.is_active = payload.is_active === "true";

    try {
      await update(`/templates/${id}`, payload, "/admin/templates");
    } catch (err: any) {
      setMsg(err?.message ?? "Failed");
    }
  }

  if (!row) {
    return (
      <AdminShell title="Edit Template" userLabel="(session)">
        <div className="text-sm text-slate-600">Loading...</div>
        <CrudMessage msg={msg} />
      </AdminShell>
    );
  }

  return (
    <AdminShell title="Edit Template" userLabel="(session)">
      <Card title={`Template: ${row.name}`}>
        <form onSubmit={onSubmit} className="space-y-3">
          <div className="grid grid-cols-1 md:grid-cols-2 gap-3">
            <Field label="Name" name="name" defaultValue={row.name} />
            <Field label="Type" name="type" defaultValue={row.type} />
            <Field label="Subject" name="subject" defaultValue={row.subject ?? ""} />
            <Field label="Active (true/false)" name="is_active" defaultValue={String(row.is_active)} />
          </div>

          <div className="space-y-1">
            <label className="text-sm">Content</label>
            <textarea
              className="w-full border rounded px-3 py-2 min-h-40"
              value={content}
              onChange={(e) => setContent(e.target.value)}
            />
          </div>

          <div className="flex items-center gap-3">
            <PrimaryButton type="submit">Update</PrimaryButton>
            <CrudMessage msg={msg} />
          </div>
        </form>
      </Card>
    </AdminShell>
  );
}