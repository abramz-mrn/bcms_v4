"use client";

import AdminShell from "@/components/AdminShell";
import Link from "next/link";
import { useEffect, useState } from "react";
import { apiGet } from "@/lib/api";
import { DangerButton, CrudMessage, useCrudSubmit } from "@/components/crud";

export default function CustomersPage() {
  const [rows, setRows] = useState<any[]>([]);
  const { msg, setMsg, remove } = useCrudSubmit();

  async function load() {
    const data = await apiGet("/customers", { credentials: "include" });
    setRows(data.data);
  }

  useEffect(() => {
    load().catch((e) => setMsg(e.message));
  }, [setMsg]);

  return (
    <AdminShell title="Customers" userLabel="(session)">
      <div className="flex justify-between mb-4">
        <CrudMessage msg={msg} />
        <Link className="px-4 py-2 rounded bg-slate-900 text-white" href="/admin/customers/new">
          Add Customer
        </Link>
      </div>

      <div className="bg-white border rounded overflow-auto">
        <table className="w-full text-sm">
          <thead className="bg-slate-100">
            <tr>
              <th className="text-left p-3">Code</th>
              <th className="text-left p-3">Name</th>
              <th className="text-left p-3">Phone</th>
              <th className="text-left p-3">Area</th>
              <th className="text-left p-3">Actions</th>
            </tr>
          </thead>
          <tbody>
            {rows.map((c) => (
              <tr key={c.id} className="border-t">
                <td className="p-3">{c.code}</td>
                <td className="p-3">{c.name}</td>
                <td className="p-3">{c.phone ?? "-"}</td>
                <td className="p-3">{c.group_area ?? "-"}</td>
                <td className="p-3 flex gap-2">
                  <Link className="px-3 py-1 rounded bg-slate-200 hover:bg-slate-300" href={`/admin/customers/${c.id}/edit`}>
                    Edit
                  </Link>
                  <DangerButton
                    onClick={async () => {
                      await remove(`/customers/${c.id}`);
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