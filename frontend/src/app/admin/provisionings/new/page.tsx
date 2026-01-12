"use client";

import AdminShell from "@/components/AdminShell";
import { Card, Field, Submit } from "@/components/ui";
import { apiPost, ensureCsrfCookie } from "@/lib/api";
import { useState } from "react";

export default function NewProvisioningPage() {
  const [msg, setMsg] = useState("");

  async function onSubmit(e: React.FormEvent<HTMLFormElement>) {
    e.preventDefault();
    setMsg("");

    const form = new FormData(e.currentTarget);
    const payload: any = Object.fromEntries(form.entries());

    try {
      await ensureCsrfCookie();
      await apiPost("/provisionings", payload, { credentials: "include" });
      window.location.href = "/admin/provisionings";
    } catch (err: any) {
      setMsg(err?.message ?? "Failed");
    }
  }

  return (
    <AdminShell title="Add Provisioning" userLabel="(session)">
      <Card title="Provisioning Form">
        <form onSubmit={onSubmit} className="space-y-3">
          <div className="grid grid-cols-1 md:grid-cols-2 gap-3">
            <Field label="Subscription ID" name="subscription_id" defaultValue="1" />
            <Field label="Subscription No (optional)" name="subscription_no" defaultValue="SUB-000001" />
            <Field label="Router ID" name="router_id" defaultValue="1" />
            <Field label="Device Conn (PPPoE/Static-IP)" name="device_conn" defaultValue="PPPoE" />
            <Field label="PPPoE Name" name="pppoe_name" defaultValue="" />
            <Field label="PPPoE Password" name="pppoe_password" defaultValue="" />
            <Field label="Static IP" name="static_ip" defaultValue="" />
            <Field label="Static Gateway" name="static_gateway" defaultValue="" />
          </div>
          <div className="flex gap-3 items-center">
            <Submit label="Save" />
            {msg && <div className="text-sm text-red-600">{msg}</div>}
          </div>
        </form>
      </Card>
    </AdminShell>
  );
}