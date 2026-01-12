"use client";

import AdminShell from "@/components/AdminShell";
import { Card, Field } from "@/components/ui";
import { CrudMessage, PrimaryButton, useCrudSubmit } from "@/components/crud";

export default function NewRouterPage() {
  const { msg, setMsg, create } = useCrudSubmit();

  async function onSubmit(e: React.FormEvent<HTMLFormElement>) {
    e.preventDefault();
    setMsg("");

    const form = new FormData(e.currentTarget);
    const payload: any = Object.fromEntries(form.entries());

    // type coercion
    if (payload.api_port) payload.api_port = Number(payload.api_port);
    if (payload.ssh_port) payload.ssh_port = Number(payload.ssh_port);

    try {
      await create("/routers", payload, "/admin/routers");
    } catch (err: any) {
      setMsg(err?.message ?? "Failed");
    }
  }

  return (
    <AdminShell title="Add Router" userLabel="(session)">
      <Card title="Router Form">
        <form onSubmit={onSubmit} className="space-y-3">
          <div className="grid grid-cols-1 md:grid-cols-2 gap-3">
            <Field label="Name" name="name" defaultValue="RTR-NEW" />
            <Field label="Location" name="location" defaultValue="POP" />
            <Field label="IP Address" name="ip_address" defaultValue="10.0.0.2" />
            <Field label="API Port" name="api_port" defaultValue="8729" />
            <Field label="SSH Port" name="ssh_port" defaultValue="22" />
            <Field label="API Username" name="api_username" defaultValue="admin" />
            <Field label="API Password" name="api_password" defaultValue="password" />
            <Field label="TLS Enabled (true/false)" name="tls_enabled" defaultValue="true" />
            <Field label="SSH Enabled (true/false)" name="ssh_enabled" defaultValue="false" />
            <Field label="Status (online/offline/maintenance)" name="status" defaultValue="offline" />
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