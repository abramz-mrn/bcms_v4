"use client";

import AdminShell from "@/components/AdminShell";
import { Card, Field } from "@/components/ui";
import { CrudMessage, PrimaryButton, useCrudSubmit } from "@/components/crud";

export default function NewInvoicePage() {
  const { msg, setMsg, create } = useCrudSubmit();

  async function onSubmit(e: React.FormEvent<HTMLFormElement>) {
    e.preventDefault();
    setMsg("");

    const form = new FormData(e.currentTarget);
    const payload: any = Object.fromEntries(form.entries());

    // ensure numeric fields are numeric
    ["amount", "tax_amount", "discount_amount", "total_amount"].forEach((k) => {
      if (payload[k] !== undefined && payload[k] !== "") payload[k] = Number(payload[k]);
    });

    try {
      await create("/invoices", payload, "/admin/invoices");
    } catch (err: any) {
      setMsg(err?.message ?? "Failed");
    }
  }

  return (
    <AdminShell title="Create Invoice" userLabel="(session)">
      <Card title="Invoice Form">
        <form onSubmit={onSubmit} className="space-y-3">
          <div className="grid grid-cols-1 md:grid-cols-2 gap-3">
            <Field label="Customer ID" name="customer_id" defaultValue="1" />
            <Field label="Subscription ID" name="subscription_id" defaultValue="1" />
            <Field label="Product ID" name="product_id" defaultValue="1" />
            <Field label="Period Start" name="period_start" type="date" />
            <Field label="Period End" name="period_end" type="date" />
            <Field label="Amount" name="amount" defaultValue="150000" />
            <Field label="Tax Amount" name="tax_amount" defaultValue="0" />
            <Field label="Discount Amount" name="discount_amount" defaultValue="0" />
            <Field label="Total Amount" name="total_amount" defaultValue="150000" />
            <Field label="Due Date" name="due_date" type="date" />
            <Field label="Status (Unpaid/Paid)" name="status" defaultValue="Unpaid" />
          </div>

          <div className="flex items-center gap-3">
            <PrimaryButton type="submit">Save</PrimaryButton>
            <CrudMessage msg={msg} />
          </div>

          <div className="text-xs text-slate-500">
            Invoice No is generated automatically as INV/TIU/YYYY/000001.
          </div>
        </form>
      </Card>
    </AdminShell>
  );
}