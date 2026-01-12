"use client";

import AdminShell from "@/components/AdminShell";
import { Card, Field } from "@/components/ui";
import { CrudMessage, PrimaryButton, useCrudSubmit } from "@/components/crud";

export default function NewBrandPage() {
  const { msg, setMsg, create } = useCrudSubmit();

  async function onSubmit(e: React.FormEvent<HTMLFormElement>) {
    e.preventDefault();
    setMsg("");

    const form = new FormData(e.currentTarget);
    const payload = Object.fromEntries(form.entries());

    try {
      await create("/brands", payload, "/admin/brands");
    } catch (err: any) {
      setMsg(err?.message ?? "Failed");
    }
  }

  return (
    <AdminShell title="Add Brand" userLabel="(session)">
      <Card title="Brand Form">
        <form onSubmit={onSubmit} className="space-y-3">
          <div className="grid grid-cols-1 md:grid-cols-2 gap-3">
            <Field label="Company ID" name="company_id" defaultValue="1" />
            <Field label="Name" name="name" defaultValue="Maroon-NET" />
            <Field label="Description" name="description" defaultValue="ISP brand" />
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