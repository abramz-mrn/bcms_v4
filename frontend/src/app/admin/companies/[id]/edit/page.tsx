"use client";

import AdminShell from "@/components/AdminShell";
import { Card, Field } from "@/components/ui";
import { CrudMessage, PrimaryButton, useCrudSubmit } from "@/components/crud";
import { apiGet } from "@/lib/api";
import { useEffect, useState } from "react";

export default function EditCompanyPage({ params }: { params: { id: string } }) {
  const id = params.id;
  const { msg, setMsg, update } = useCrudSubmit();
  const [row, setRow] = useState<any | null>(null);

  useEffect(() => {
    apiGet(`/companies/${id}`, { credentials: "include" })
      .then(setRow)
      .catch((e) => setMsg(e.message));
  }, [id, setMsg]);

  async function onSubmit(e: React.FormEvent<HTMLFormElement>) {
    e.preventDefault();
    setMsg("");

    const form = new FormData(e.currentTarget);
    const payload = Object.fromEntries(form.entries());

    try {
      await update(`/companies/${id}`, payload, "/admin/companies");
    } catch (err: any) {
      setMsg(err?.message ?? "Failed");
    }
  }

  if (!row) {
    return (
      <AdminShell title="Edit Company" userLabel="(session)">
        <div className="text-sm text-slate-600">Loading...</div>
        <CrudMessage msg={msg} />
      </AdminShell>
    );
  }

  return (
    <AdminShell title="Edit Company" userLabel="(session)">
      <Card title={`Company: ${row.name}`}>
        <form onSubmit={onSubmit} className="space-y-3">
          <div className="grid grid-cols-1 md:grid-cols-2 gap-3">
            <Field label="Name" name="name" defaultValue={row.name} />
            <Field label="Initial" name="initial" defaultValue={row.initial ?? ""} />
            <Field label="City" name="city" defaultValue={row.city ?? ""} />
            <Field label="State" name="state" defaultValue={row.state ?? ""} />
            <Field label="POS" name="pos" defaultValue={row.pos ?? ""} />
            <Field label="Address" name="address" defaultValue={row.address ?? ""} />
            <Field label="NPWP" name="npwp" defaultValue={row.npwp ?? ""} />
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