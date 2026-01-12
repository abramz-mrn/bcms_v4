"use client";

import AdminShell from "@/components/AdminShell";
import { Card, Field } from "@/components/ui";
import { CrudMessage, PrimaryButton, useCrudSubmit } from "@/components/crud";

export default function NewSubscriptionPage() {
  const { msg, setMsg, create } = useCrudSubmit();

  async function onSubmit(e: React.FormEvent<HTMLFormElement>) {
    e.preventDefault();
    setMsg("");

    const form = new FormData(e.currentTarget);
    const payload = Object.fromEntries(form.entries());

    try {
      await create("/subscriptions", payload, "/admin/subscriptions");
    } catch (err: any) {
      setMsg(err?.message ?? "Failed");
    }
  }

  return (
    <AdminShell title="Add Subscription" userLabel="(session)">
      <Card title="Subscription Form">
        <form onSubmit={onSubmit} className="space-y-3">
          <div className="grid grid-cols-1 md:grid-cols-2 gap-3">
            <Field label="Subscription No" name="subscription_no" defaultValue="SUB-NEW" />
            <Field label="Customer ID" name="customer_id" defaultValue="1" />
            <Field label="Product ID" name="product_id" defaultValue="1" />
            <Field label="Registration Date" name="registration_date" type="date" />
            <Field label="Status" name="status" defaultValue="Registered" />
          </div>

          <div className="flex items-center gap-3">
            <PrimaryButton type="submit">Save</PrimaryButton>
            <CrudMessage msg={msg} />
          </div>

          <div className="text-xs text-slate-500">
            Notes: consent fields not shown here (starter). You can edit via API or we can add toggles next.
          </div>
        </form>
      </Card>
    </AdminShell>
  );
}