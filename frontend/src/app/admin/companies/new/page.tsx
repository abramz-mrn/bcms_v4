"use client";

import AdminShell from "@/components/AdminShell";
import { Card, Field } from "@/components/ui";
import { CrudMessage, PrimaryButton, useCrudSubmit } from "@/components/crud";

export default function NewCompanyPage() {
  const { msg, setMsg, create } = useCrudSubmit();

  async function onSubmit(e: React.FormEvent<HTMLFormElement>) {
    e.preventDefault();
    setMsg("");

    const form = new FormData(e.currentTarget);
    const payload = Object.fromEntries(form.entries());

    try {
      await create("/companies", payload, "/admin/companies");
    } catch (err: any) {
      setMsg(err?.message ?? "Failed");
    }
  }

  return (
    <AdminShell title="Add Company" userLabel="(session)">
      <Card title="Company Form">
        <form onSubmit={onSubmit} className="space-y-3">
          <div className="grid grid-cols-1 md:grid-cols-2 gap-3">
            <Field label="Name" name="name" defaultValue="PT. Trira Inti Utama" />
            <Field label="Initial" name="initial" defaultValue="TIU" />
            <Field label="City" name="city" defaultValue="Kab. Bekasi" />
            <Field label="State" name="state" defaultValue="Jawa Barat" />
            <Field label="POS" name="pos" defaultValue="17530" />
            <Field label="Address" name="address" defaultValue="Ruko Kemanggisan Blok O4 No. 6 Metland Cibitung" />
            <Field label="NPWP" name="npwp" defaultValue="50.520.877.7-413.000" />
          </div>

          <div className="flex items-center gap-3">
            <PrimaryButton type="submit">Save</PrimaryButton>
            <CrudMessage msg={msg} />
          </div>

          <div className="text-xs text-slate-500">
            bank_account is JSON; not editable in this starter form (can be added next).
          </div>
        </form>
      </Card>
    </AdminShell>
  );
}