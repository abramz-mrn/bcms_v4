"use client";

import AdminShell from "@/components/AdminShell";
import Link from "next/link";
import { useEffect, useState } from "react";
import { apiGet } from "@/lib/api";
import { CrudMessage, DangerButton, PrimaryButton, useCrudSubmit } from "@/components/crud";

export default function PaymentsPage() {
  const [rows, setRows] = useState<any[]>([]);
  const { msg, setMsg, remove, update } = useCrudSubmit();

  async function load() {
    const data = await apiGet("/payments", { credentials: "include" });
    setRows(data.data);
  }

  useEffect(() => {
    load().catch((e) => setMsg(e.message));
  }, [setMsg]);

  async function quickSet(id: number, status: "verified" | "rejected") {
    try {
      await update(`/payments/${id}`, { status }, "/admin/payments");
      await load();
    } catch (e: any) {
      setMsg(e?.message ?? "Failed");
    }
  }

  return (
    <AdminShell title="Payments" userLabel="(session)">
      <div className="flex justify-between mb-4">
        <CrudMessage msg={msg} />
        <Link className="px-4 py-2 rounded bg-slate-900 text-white" href="/admin/payments/new">
          Add Payment
        </Link>
      </div>

      <div className="bg-white border rounded overflow-auto">
        <table className="w-full text-sm">
          <thead className="bg-slate-100">
            <tr>
              <th className="text-left p-3">Invoice</th>
              <th className="text-left p-3">Method</th>
              <th className="text-left p-3">Gateway</th>
              <th className="text-left p-3">Amount</th>
              <th className="text-left p-3">Status</th>
              <th className="text-left p-3">Actions</th>
            </tr>
          </thead>
          <tbody>
            {rows.map((p) => (
              <tr key={p.id} className="border-t">
                <td className="p-3">{p.invoice?.invoice_no ?? `#${p.invoice_id}`}</td>
                <td className="p-3">{p.payment_method}</td>
                <td className="p-3">{p.payment_gateway ?? "-"}</td>
                <td className="p-3">{p.amount_paid}</td>
                <td className="p-3">{p.status}</td>
                <td className="p-3 flex gap-2 flex-wrap">
                  <Link className="px-3 py-1 rounded bg-slate-200 hover:bg-slate-300" href={`/admin/payments/${p.id}/edit`}>
                    Edit
                  </Link>

                  <PrimaryButton
                    type="button"
                    onClick={async () => quickSet(p.id, "verified")}
                  >
                    Verify
                  </PrimaryButton>

                  <button
                    onClick={async (e) => {
                      e.preventDefault();
                      await quickSet(p.id, "rejected");
                    }}
                    className="px-3 py-1 rounded bg-orange-600 text-white hover:bg-orange-700"
                  >
                    Reject
                  </button>

                  <DangerButton
                    onClick={async () => {
                      await remove(`/payments/${p.id}`);
                      await load();
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
        Verify will set payment status=verified and backend will also mark invoice Paid (starter behavior).
      </div>
    </AdminShell>
  );
}