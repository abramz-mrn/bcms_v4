"use client";

import AdminShell from "@/components/AdminShell";
import { Card, Field } from "@/components/ui";
import { CrudMessage, PrimaryButton, useCrudSubmit } from "@/components/crud";
import { apiGet } from "@/lib/api";
import { useEffect, useState } from "react";

export default function EditTicketPage({ params }: { params: { id: string } }) {
  const id = params.id;
  const { msg, setMsg, update } = useCrudSubmit();
  const [row, setRow] = useState<any | null>(null);

  useEffect(() => {
    apiGet(`/tickets/${id}`, { credentials: "include" })
      .then(setRow)
      .catch((e) => setMsg(e.message));
  }, [id, setMsg]);

  async function onSubmit(e: React.FormEvent<HTMLFormElement>) {
    e.preventDefault();
    setMsg("");

    const form = new FormData(e.currentTarget);
    const payload = Object.fromEntries(form.entries());

    try {
      await update(`/tickets/${id}`, payload, "/admin/tickets");
    } catch (err: any) {
      setMsg(err?.message ?? "Failed");
    }
  }

  if (!row) {
    return (
      <AdminShell title="Edit Ticket" userLabel="(session)">
        <div className="text-sm text-slate-600">Loading...</div>
        <CrudMessage msg={msg} />
      </AdminShell>
    );
  }

  return (
    <AdminShell title="Edit Ticket" userLabel="(session)">
      <Card title={`Ticket: ${row.ticket_number}`}>
        <form onSubmit={onSubmit} className="space-y-3">
          <div className="grid grid-cols-1 md:grid-cols-2 gap-3">
            <Field label="Status" name="status" defaultValue={row.status} />
            <Field label="Assigned To (User ID)" name="assigned_to" defaultValue={row.assigned_to ?? ""} />
            <Field label="Assigned At (date)" name="assigned_at" type="date" defaultValue={(row.assigned_at ?? "").slice(0, 10)} />
            <Field label="Resolved At (date)" name="resolved_at" type="date" defaultValue={(row.resolved_at ?? "").slice(0, 10)} />
            <Field label="Closed At (date)" name="closed_at" type="date" defaultValue={(row.closed_at ?? "").slice(0, 10)} />
            <Field label="Resolution Notes" name="resolution_notes" defaultValue={row.resolution_notes ?? ""} />
            <Field label="Customer Rating (1-5)" name="customer_rating" defaultValue={row.customer_rating ?? ""} />
            <Field label="Customer Feedback" name="customer_feedback" defaultValue={row.customer_feedback ?? ""} />
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