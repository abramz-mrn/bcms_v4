"use client";

import AdminShell from "@/components/AdminShell";
import { Card, Field } from "@/components/ui";
import { CrudMessage, PrimaryButton, useCrudSubmit } from "@/components/crud";

export default function NewReminderPage() {
  const { msg, setMsg, create } = useCrudSubmit();

  async function onSubmit(e: React.FormEvent<HTMLFormElement>) {
    e.preventDefault();
    setMsg("");

    const form = new FormData(e.currentTarget);
    const payload = Object.fromEntries(form.entries());

    try {
      await create("/reminders", payload, "/admin/reminders");
    } catch (err: any) {
      setMsg(err?.message ?? "Failed");
    }
  }

  return (
    <AdminShell title="Add Reminder" userLabel="(session)">
      <Card title="Reminder Form (Manual)">
        <form onSubmit={onSubmit} className="space-y-3">
          <div className="grid grid-cols-1 md:grid-cols-2 gap-3">
            <Field label="Invoice ID" name="invoice_id" defaultValue="1" />
            <Field label="Template ID" name="template_id" defaultValue="1" />
            <Field label="Channel (email/sms/whatsapp)" name="channel" defaultValue="whatsapp" />
            <Field label="Trigger Type" name="trigger_type" defaultValue="before_due" />
            <Field label="Days Offset" name="days_offset" defaultValue="-1" />
            <Field label="Scheduled At (YYYY-MM-DD)" name="scheduled_at" type="date" />
            <Field label="Status" name="status" defaultValue="pending" />
          </div>

          <div className="flex items-center gap-3">
            <PrimaryButton type="submit">Save</PrimaryButton>
            <CrudMessage msg={msg} />
          </div>

          <div className="text-xs text-slate-500">
            Normally reminders are created automatically by ScheduleRemindersJob.
          </div>
        </form>
      </Card>
    </AdminShell>
  );
}