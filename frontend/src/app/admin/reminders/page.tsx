"use client";

import AdminShell from "@/components/AdminShell";
import Link from "next/link";
import { useEffect, useMemo, useState } from "react";
import { apiGet } from "@/lib/api";
import { CrudMessage, DangerButton, PrimaryButton, useCrudSubmit } from "@/components/crud";
import { useRouter, useSearchParams } from "next/navigation";

const STATUSES = ["all", "pending", "sent", "failed", "skipped", "cancelled"] as const;

export default function RemindersPage() {
  const [rows, setRows] = useState<any[]>([]);
  const { msg, setMsg, remove, update } = useCrudSubmit();
  const router = useRouter();
  const sp = useSearchParams();

  const statusParam = sp.get("status") || "all";
  const [status, setStatus] = useState<string>(statusParam);

  async function load(currentStatus: string) {
    const qs = currentStatus && currentStatus !== "all" ? `?status=${encodeURIComponent(currentStatus)}` : "";
    const data = await apiGet(`/reminders${qs}`, { credentials: "include" });

    // Fallback filter (if backend ignores status)
    const list = data.data ?? [];
    const filtered = currentStatus === "all" ? list : list.filter((r: any) => r.status === currentStatus);

    setRows(filtered);
  }

  useEffect(() => {
    // sync local select with query param
    setStatus(statusParam);
    load(statusParam).catch((e) => setMsg(e.message));
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, [statusParam]);

  async function retry(id: number) {
    try {
      await update(`/reminders/${id}`, { status: "pending", sent_at: null, error_message: null }, "/admin/reminders");
      await load(statusParam);
    } catch (e: any) {
      setMsg(e?.message ?? "Retry failed");
    }
  }

  async function cancel(id: number) {
    try {
      await update(`/reminders/${id}`, { status: "cancelled" }, "/admin/reminders");
      await load(statusParam);
    } catch (e: any) {
      setMsg(e?.message ?? "Cancel failed");
    }
  }

  const titleSuffix = useMemo(() => (statusParam !== "all" ? ` (status: ${statusParam})` : ""), [statusParam]);

  return (
    <AdminShell title={`Reminders${titleSuffix}`} userLabel="(session)">
      <div className="flex flex-col md:flex-row md:items-center md:justify-between gap-3 mb-4">
        <CrudMessage msg={msg} />

        <div className="flex gap-2 flex-wrap items-center">
          <div className="text-sm text-slate-600">Filter:</div>
          <select
            className="border rounded px-3 py-2 text-sm bg-white"
            value={status}
            onChange={(e) => setStatus(e.target.value)}
          >
            {STATUSES.map((s) => (
              <option key={s} value={s}>
                {s}
              </option>
            ))}
          </select>

          <button
            className="px-3 py-2 rounded bg-slate-800 text-white hover:bg-slate-700 text-sm"
            onClick={() => {
              const q = status && status !== "all" ? `?status=${encodeURIComponent(status)}` : "";
              router.push(`/admin/reminders${q}`);
            }}
          >
            Apply
          </button>

          <button
            className="px-3 py-2 rounded border bg-white hover:bg-slate-50 text-sm"
            onClick={() => router.push("/admin/reminders")}
          >
            Reset
          </button>

          <Link className="px-4 py-2 rounded bg-slate-900 text-white text-sm" href="/admin/reminders/new">
            Add Reminder
          </Link>
        </div>
      </div>

      <div className="bg-white border rounded overflow-auto">
        <table className="w-full text-sm">
          <thead className="bg-slate-100">
            <tr>
              <th className="text-left p-3">Invoice</th>
              <th className="text-left p-3">Channel</th>
              <th className="text-left p-3">Trigger</th>
              <th className="text-left p-3">Offset</th>
              <th className="text-left p-3">Scheduled</th>
              <th className="text-left p-3">Status</th>
              <th className="text-left p-3">Error</th>
              <th className="text-left p-3">Quick Actions</th>
              <th className="text-left p-3">Manage</th>
            </tr>
          </thead>
          <tbody>
            {rows.map((r) => (
              <tr key={r.id} className="border-t">
                <td className="p-3">{r.invoice?.invoice_no ?? `#${r.invoice_id}`}</td>
                <td className="p-3">{r.channel}</td>
                <td className="p-3">{r.trigger_type}</td>
                <td className="p-3">{r.days_offset}</td>
                <td className="p-3">{r.scheduled_at}</td>
                <td className="p-3">{r.status}</td>
                <td className="p-3 max-w-xs truncate" title={r.error_message ?? ""}>
                  {r.error_message ?? "-"}
                </td>

                <td className="p-3">
                  <div className="flex gap-2 flex-wrap">
                    <PrimaryButton onClick={async () => retry(r.id)}>Retry</PrimaryButton>
                    <button
                      onClick={async (e) => {
                        e.preventDefault();
                        await cancel(r.id);
                      }}
                      className="px-3 py-1 rounded bg-orange-600 text-white hover:bg-orange-700"
                    >
                      Cancel
                    </button>
                  </div>
                </td>

                <td className="p-3 flex gap-2">
                  <Link className="px-3 py-1 rounded bg-slate-200 hover:bg-slate-300" href={`/admin/reminders/${r.id}/edit`}>
                    Edit
                  </Link>
                  <DangerButton
                    onClick={async () => {
                      await remove(`/reminders/${r.id}`);
                      await load(statusParam);
                    }}
                  >
                    Delete
                  </DangerButton>
                </td>
              </tr>
            ))}
          </tbody>
        </table>
      </div>

      <div className="text-xs text-slate-500 mt-3">
        Status: pending, sent, failed, skipped, cancelled. Filter uses ?status=...
      </div>
    </AdminShell>
  );
}