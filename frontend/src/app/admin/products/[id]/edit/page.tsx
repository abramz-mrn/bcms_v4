"use client";

import AdminShell from "@/components/AdminShell";
import { Card, Field } from "@/components/ui";
import { CrudMessage, PrimaryButton, useCrudSubmit } from "@/components/crud";
import { apiGet } from "@/lib/api";
import { useEffect, useState } from "react";
import { parseRupiahDecimalToInt } from "@/lib/money";

export default function EditProductPage({ params }: { params: { id: string } }) {
  const id = params.id;
  const { msg, setMsg, update } = useCrudSubmit();
  const [row, setRow] = useState<any | null>(null);

  useEffect(() => {
    apiGet(`/products/${id}`, { credentials: "include" })
      .then(setRow)
      .catch((e) => setMsg(e.message));
  }, [id, setMsg]);

  async function onSubmit(e: React.FormEvent<HTMLFormElement>) {
    e.preventDefault();
    setMsg("");

    const form = new FormData(e.currentTarget);
    const payload: any = Object.fromEntries(form.entries());

    payload.price = parseRupiahDecimalToInt(String(payload.price || "0"));

    try {
      await update(`/products/${id}`, payload, "/admin/products");
    } catch (err: any) {
      setMsg(err?.message ?? "Failed");
    }
  }

  if (!row) {
    return (
      <AdminShell title="Edit Product" userLabel="(session)">
        <div className="text-sm text-slate-600">Loading...</div>
        <CrudMessage msg={msg} />
      </AdminShell>
    );
  }

  return (
    <AdminShell title="Edit Product" userLabel="(session)">
      <Card title={`Product: ${row.code}`}>
        <form onSubmit={onSubmit} className="space-y-3">
          <div className="grid grid-cols-1 md:grid-cols-2 gap-3">
            <Field label="Code" name="code" defaultValue={row.code} />
            <Field label="Name" name="name" defaultValue={row.name} />
            <Field label="Type" name="type" defaultValue={row.type} />
            <Field label="Market Segment" name="market_segment" defaultValue={row.market_segment ?? ""} />
            <Field label="Billing Cycle" name="billing_cycle" defaultValue={row.billing_cycle} />
            <Field label="Price (decimal UI, Rp)" name="price" defaultValue={`${row.price}.00`} />
            <Field label="Tax Rate (%)" name="tax_rate" defaultValue={String(row.tax_rate ?? 0)} />
            <Field label="Tax Included (true/false)" name="tax_included" defaultValue={String(row.tax_included ?? false)} />
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