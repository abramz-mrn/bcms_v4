"use client";

import AdminShell from "@/components/AdminShell";
import { Card, Field } from "@/components/ui";
import { CrudMessage, PrimaryButton, useCrudSubmit } from "@/components/crud";
import { apiGet } from "@/lib/api";
import { useEffect, useState } from "react";

export default function EditRouterPage({ params }: { params: { id: string } }) {
  const id = params.id;
  const { msg, setMsg, update } = useCrudSubmit();
  const [row, setRow] = useState<any | null>(null);

  useEffect(() => {
    apiGet(`/routers/${id}`, { credentials: "include" })
      .then(setRow)
      .catch((e) => setMsg(e.message));
  }, [id, setMsg]);

  async function onSubmit(e: React.FormEvent<HTMLFormElement>) {
    e.preventDefault();
    setMsg("");

    const form = new FormData(e.currentTarget);
    const payload: any = Object.fromEntries(form.entries());

    if (payload.api_port) payload.api_port = Number(payload.api_port);
    if (payload.ssh_port) payload.ssh_port = Number(payload.ssh_port);

    try {
      await update(`/routers/${id}`, payload, "/admin/routers");
    } catch (err: any) {
      setMsg(err?.message ?? "Failed");
    }
  }

  if (!row) {
    return (
      <AdminShell title="Edit Router" userLabel="(session)">
        <div className="text-sm text-slate-600">Loading...</div>
        <CrudMessage msg={msg} />
      </AdminShell>
    );
  }

  return (
    <AdminShell title="Edit Router" userLabel="(session)">
      <Card title={`Router: ${row.name}`}>
        <form onSubmit={onSubmit} className="space-y-3">
          <div className="grid grid-cols-1 md:grid-cols-2 gap-3">
            <Field label="Name" name="name" defaultValue={row.name} />
            <Field label="Location" name="location" defaultValue={row.location ?? ""} />
            <Field label="IP Address" name="ip_address" defaultValue={row.ip_address} />
            <Field label="API Port" name="api_port" defaultValue={String(row.api_port)} />
            <Field label="SSH Port" name="ssh_port" defaultValue={String(row.ssh_port)} />
            <Field label="API Username" name="api_username" defaultValue={row.api_username} />
            <Field label="API Password" name="api_password" defaultValue={row.api_password} />
            <Field label="TLS Enabled (true/false)" name="tls_enabled" defaultValue={String(row.tls_enabled)} />
            <Field label="SSH Enabled (true/false)" name="ssh_enabled" defaultValue={String(row.ssh_enabled)} />
            <Field label="Status" name="status" defaultValue={row.status} />
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