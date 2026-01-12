"use client";

import AdminShell from "@/components/AdminShell";
import { Card, Field } from "@/components/ui";
import { CrudMessage, PrimaryButton, useCrudSubmit } from "@/components/crud";
import { apiGet } from "@/lib/api";
import { useEffect, useState } from "react";

export default function EditProvisioningPage({ params }: { params: { id: string } }) {
  const id = params.id;
  const { msg, setMsg, update } = useCrudSubmit();
  const [row, setRow] = useState<any | null>(null);

  useEffect(() => {
    apiGet(`/provisionings/${id}`, { credentials: "include" })
      .then(setRow)
      .catch((e) => setMsg(e.message));
  }, [id, setMsg]);

  async function onSubmit(e: React.FormEvent<HTMLFormElement>) {
    e.preventDefault();
    setMsg("");

    const form = new FormData(e.currentTarget);
    const payload = Object.fromEntries(form.entries());

    try {
      await update(`/provisionings/${id}`, payload, "/admin/provisionings");
    } catch (err: any) {
      setMsg(err?.message ?? "Failed");
    }
  }

  if (!row) {
    return (
      <AdminShell title="Edit Provisioning" userLabel="(session)">
        <div className="text-sm text-slate-600">Loading...</div>
        <CrudMessage msg={msg} />
      </AdminShell>
    );
  }

  return (
    <AdminShell title="Edit Provisioning" userLabel="(session)">
      <Card title={`Provisioning #${row.id}`}>
        <form onSubmit={onSubmit} className="space-y-3">
          <div className="grid grid-cols-1 md:grid-cols-2 gap-3">
            <Field label="Router ID" name="router_id" defaultValue={String(row.router_id)} />
            <Field label="Device Conn" name="device_conn" defaultValue={row.device_conn} />
            <Field label="PPPoE Name" name="pppoe_name" defaultValue={row.pppoe_name ?? ""} />
            <Field label="PPPoE Password" name="pppoe_password" defaultValue={row.pppoe_password ?? ""} />
            <Field label="Static IP" name="static_ip" defaultValue={row.static_ip ?? ""} />
            <Field label="Static Gateway" name="static_gateway" defaultValue={row.static_gateway ?? ""} />
            <Field label="Technician" name="technisian_name" defaultValue={row.technisian_name ?? ""} />
          </div>

          <div className="flex items-center gap-3">
            <PrimaryButton type="submit">Update</PrimaryButton>
            <CrudMessage msg={msg} />
          </div>

          <div className="text-xs text-slate-500">
            Subscription change is not editable here (starter). If needed, we can add it.
          </div>
        </form>
      </Card>
    </AdminShell>
  );
}