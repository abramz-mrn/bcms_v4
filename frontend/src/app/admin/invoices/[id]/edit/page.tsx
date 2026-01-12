"use client";

import AdminShell from "@/components/AdminShell";
import { Card, Field } from "@/components/ui";
import { CrudMessage, PrimaryButton, useCrudSubmit } from "@/components/crud";
import { apiGet } from "@/lib/api";
import { useEffect, useState } from "react";

export default function EditInvoicePage({ params }: { params: { id: string } }) {
  const id = params.id;
  const { msg, setMsg, update } = useCrudSubmit();
  const [row, setRow] = useState<any | null>(null);

  useEffect(() => {
    apiGet(`/invoices/${id}`, { credentials: "include" })
      .then(setRow)
      .catch((e) => setMsg(e.message));
  }, [id, setMsg]);

  async function onSubmit(e: React.FormEvent<HTMLFormElement>) {
    e.preventDefault();
    setMsg("");

    const form = new FormData(e.currentTarget);
    const payload: any = Object.fromEntries(form.entries());

    ["amount", "tax_amount", "discount_amount", "total_amount"].forEach((k) => {
      if (payload[k] !== undefined && payload[k] !== "") payload[k] = Number(payload[k]);
    });

    try {
      await update(`/invoices/${id}`, payload, "/admin/invoices");
    } catch (err: any) {
      setMsg(err?.message ?? "Failed");
    }
  }

  if (!row) {
    return (
      <AdminShell title="Edit Invoice" userLabel="(session)">
        <div className="text-sm text-slate-600">Loading...</div>
        <CrudMessage msg={msg} />
      </AdminShell>
    );
  }

  return (
    <AdminShell title="Edit Invoice" userLabel="(session)">
      <Card title={`Invoice: ${row.invoice_no}`}>
        <form onSubmit={onSubmit} className="space-y-3">
          <div className="grid grid-cols-1 md:grid-cols-2 gap-3">
            <Field label="Period Start" name="period_start" type="date" defaultValue={(row.period_start ?? "").slice(0, 10)} />
            <Field label="Period End" name="period_end" type="date" defaultValue={(row.period_end ?? "").slice(0, 10)} />
            <Field label="Amount" name="amount" defaultValue={String(row.amount)} />
            <Field label="Tax Amount" name="tax_amount" defaultValue={String(row.tax_amount ?? 0)} />
            <Field label="Discount Amount" name="discount_amount" defaultValue={String(row.discount_amount ?? 0)} />
            <Field label="Total Amount" name="total_amount" defaultValue={String(row.total_amount)} />
            <Field label="Due Date" name="due_date" type="date" defaultValue={(row.due_date ?? "").slice(0, 10)} />
            <Field label="Status (Unpaid/Paid)" name="status" defaultValue={row.status} />
          </div>

          <div className="flex items-center gap-3">
            <PrimaryButton type="submit">Update</PrimaryButton>
            <CrudMessage msg={msg} />
          </div>

          <div className="text-xs text-slate-500">
            Customer/Product/Subscription not editable here (starter). We can add if you want.
          </div>
        </form>
      </Card>
    </AdminShell>
  );
}