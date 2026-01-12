"use client";

import AdminShell from "@/components/AdminShell";
import { Card, Field } from "@/components/ui";
import { CrudMessage, PrimaryButton, useCrudSubmit } from "@/components/crud";

export default function NewPaymentPage() {
  const { msg, setMsg, create } = useCrudSubmit();

  async function onSubmit(e: React.FormEvent<HTMLFormElement>) {
    e.preventDefault();
    setMsg("");

    const form = new FormData(e.currentTarget);
    const payload: any = Object.fromEntries(form.entries());
    payload.amount_paid = Number(payload.amount_paid);

    try {
      await create("/payments", payload, "/admin/payments");
    } catch (err: any) {
      setMsg(err?.message ?? "Failed");
    }
  }

  return (
    <AdminShell title="Add Payment" userLabel="(session)">
      <Card title="Payment Form">
        <form onSubmit={onSubmit} className="space-y-3">
          <div className="grid grid-cols-1 md:grid-cols-2 gap-3">
            <Field label="Invoice ID" name="invoice_id" defaultValue="1" />
            <Field label="Payment Method (cash/transfer/virtual account)" name="payment_method" defaultValue="cash" />
            <Field label="Gateway (optional)" name="payment_gateway" defaultValue="" />
            <Field label="Transaction ID (optional)" name="transaction_id" defaultValue="" />
            <Field label="Amount Paid" name="amount_paid" defaultValue="150000" />
            <Field label="Status (pending/verified/rejected/refunded)" name="status" defaultValue="pending" />
            <Field label="Ref Number (optional)" name="ref_number" defaultValue="" />
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