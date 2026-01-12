"use client";

import AdminShell from "@/components/AdminShell";
import { Card, Field } from "@/components/ui";
import { CrudMessage, PrimaryButton, useCrudSubmit } from "@/components/crud";
import { parseRupiahDecimalToInt } from "@/lib/money";

export default function NewProductPage() {
  const { msg, setMsg, create } = useCrudSubmit();

  async function onSubmit(e: React.FormEvent<HTMLFormElement>) {
    e.preventDefault();
    setMsg("");

    const form = new FormData(e.currentTarget);
    const payload: any = Object.fromEntries(form.entries());

    payload.price = parseRupiahDecimalToInt(String(payload.price || "0"));

    try {
      await create("/products", payload, "/admin/products");
    } catch (err: any) {
      setMsg(err?.message ?? "Failed");
    }
  }

  return (
    <AdminShell title="Add Product" userLabel="(session)">
      <Card title="Product Form">
        <form onSubmit={onSubmit} className="space-y-3">
          <div className="grid grid-cols-1 md:grid-cols-2 gap-3">
            <Field label="Code" name="code" defaultValue="NEW-PROD" />
            <Field label="Name" name="name" defaultValue="" />
            <Field label="Type" name="type" defaultValue="Internet Services" />
            <Field label="Market Segment" name="market_segment" defaultValue="Residensial" />
            <Field label="Billing Cycle" name="billing_cycle" defaultValue="Monthly" />
            <Field label="Price (decimal UI, Rp)" name="price" defaultValue="150000.00" />
            <Field label="Tax Rate (%)" name="tax_rate" defaultValue="11" />
            <Field label="Tax Included (true/false)" name="tax_included" defaultValue="false" />
          </div>

          <div className="flex items-center gap-3">
            <PrimaryButton type="submit">Save</PrimaryButton>
            <CrudMessage msg={msg} />
          </div>

          <div className="text-xs text-slate-500">
            Saved as integer rupiah in DB. Example: 150000.00 → 150000.
          </div>
        </form>
      </Card>
    </AdminShell>
  );
}