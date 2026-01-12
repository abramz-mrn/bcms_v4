"use client";

import AdminShell from "@/components/AdminShell";
import Link from "next/link";
import { useEffect, useState } from "react";
import { apiGet } from "@/lib/api";
import { CrudMessage, DangerButton, useCrudSubmit } from "@/components/crud";

export default function InvoicesPage() {
  const [rows, setRows] = useState<any[]>([]);
  const { msg, setMsg, remove } = useCrudSubmit();

  async function load() {
    const data = await apiGet("/invoices", { credentials: "include" });
    setRows(data.data);
  }

  useEffect(() => {
    load().catch((e) => setMsg(e.message));
  }, [setMsg]);

  return (
    <AdminShell title="Invoices" userLabel="(session)">
      <div className="flex justify-between mb-4">
        <CrudMessage msg={msg} />
        <Link className="px-4 py-2 rounded bg-slate-900 text-white" href="/admin/invoices/new">
          Create Invoice
        </Link>
      </div>

      <div className="bg-white border rounded overflow-auto">
        <table className="w-full text-sm">
          <thead className="bg-slate-100">
            <tr>
              <th className="text-left p-3">Invoice No</th>
              <th className="text-left p-3">Customer</th>
              <th className="text-left p-3">Total</th>
              <th className="text-left p-3">Due Date</th>
              <th className="text-left p-3">Status</th>
              <th className="text-left p-3">Actions</th>
            </tr>
          </thead>
          <tbody>
            {rows.map((inv) => (
              <tr key={inv.id} className="border-t">
                <td className="p-3">{inv.invoice_no}</td>
                <td className="p-3">{inv.customer?.name ?? `#${inv.customer_id}`}</td>
                <td className="p-3">{inv.total_amount}</td>
                <td className="p-3">{inv.due_date}</td>
                <td className="p-3">{inv.status}</td>
                <td className="p-3 flex gap-2">
                  <Link className="px-3 py-1 rounded bg-slate-200 hover:bg-slate-300" href={`/admin/invoices/${inv.id}/edit`}>
                    Edit
                  </Link>
                  <DangerButton
                    onClick={async () => {
                      await remove(`/invoices/${inv.id}`);
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
    </AdminShell>
  );
}