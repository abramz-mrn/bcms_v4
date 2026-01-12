"use client";

import AdminShell from "@/components/AdminShell";
import { Card, Field } from "@/components/ui";
import { CrudMessage, PrimaryButton, useCrudSubmit } from "@/components/crud";

export default function NewTicketPage() {
  const { msg, setMsg, create } = useCrudSubmit();

  async function onSubmit(e: React.FormEvent<HTMLFormElement>) {
    e.preventDefault();
    setMsg("");

    const form = new FormData(e.currentTarget);
    const payload = Object.fromEntries(form.entries());

    try {
      await create("/tickets", payload, "/admin/tickets");
    } catch (err: any) {
      setMsg(err?.message ?? "Failed");
    }
  }

  return (
    <AdminShell title="Create Ticket" userLabel="(session)">
      <Card title="Ticket Form">
        <form onSubmit={onSubmit} className="space-y-3">
          <div className="grid grid-cols-1 md:grid-cols-2 gap-3">
            <Field label="Customer ID" name="customer_id" defaultValue="1" />
            <Field label="Product ID (optional)" name="product_id" defaultValue="" />
            <Field label="Category (technical/billing/...)" name="category" defaultValue="technical" />
            <Field label="Priority (Low/Medium/High)" name="priority" defaultValue="High" />
            <Field label="Subject" name="subject" defaultValue="Internet down" />
            <Field label="Caller Name" name="caller_name" defaultValue="" />
            <Field label="Phone" name="phone" defaultValue="" />
            <Field label="Email" name="email" defaultValue="" />
            <Field label="SLA Due Date" name="sla_due_date" type="date" />
          </div>

          <div className="flex items-center gap-3">
            <PrimaryButton type="submit">Save</PrimaryButton>
            <CrudMessage msg={msg} />
          </div>
        </form>
      </Card>
    </AdminShell>
  );
}