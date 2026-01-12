"use client";

import AdminShell from "@/components/AdminShell";
import { Card, Field } from "@/components/ui";
import { CrudMessage, PrimaryButton, useCrudSubmit } from "@/components/crud";
import { useState } from "react";

export default function NewTemplatePage() {
  const { msg, setMsg, create } = useCrudSubmit();
  const [content, setContent] = useState("Hello {{customer_name}} ...");

  async function onSubmit(e: React.FormEvent<HTMLFormElement>) {
    e.preventDefault();
    setMsg("");

    const form = new FormData(e.currentTarget);
    const payload: any = Object.fromEntries(form.entries());
    payload.content = content;
    payload.variables = ["customer_name", "invoice_no", "total_amount", "due_date"];
    payload.is_active = payload.is_active === "true";

    try {
      await create("/templates", payload, "/admin/templates");
    } catch (err: any) {
      setMsg(err?.message ?? "Failed");
    }
  }

  return (
    <AdminShell title="Add Template" userLabel="(session)">
      <Card title="Template Form">
        <form onSubmit={onSubmit} className="space-y-3">
          <div className="grid grid-cols-1 md:grid-cols-2 gap-3">
            <Field label="Name" name="name" defaultValue="Reminder Template" />
            <Field label="Type (email/sms/whatsapp)" name="type" defaultValue="email" />
            <Field label="Subject (optional)" name="subject" defaultValue="Reminder Invoice {{invoice_no}}" />
            <Field label="Active (true/false)" name="is_active" defaultValue="true" />
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
            <PrimaryButton type="submit">Save</PrimaryButton>
            <CrudMessage msg={msg} />
          </div>

          <div className="text-xs text-slate-500">
            variables are preset in this starter; can be enhanced to editable chips later.
          </div>
        </form>
      </Card>
    </AdminShell>
  );
}