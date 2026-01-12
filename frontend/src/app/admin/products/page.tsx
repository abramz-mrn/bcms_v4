"use client";

import AdminShell from "@/components/AdminShell";
import Link from "next/link";
import { useEffect, useState } from "react";
import { apiGet } from "@/lib/api";
import { CrudMessage, DangerButton, useCrudSubmit } from "@/components/crud";

export default function ProductsPage() {
  const [rows, setRows] = useState<any[]>([]);
  const { msg, setMsg, remove } = useCrudSubmit();

  async function load() {
    const data = await apiGet("/products", { credentials: "include" });
    setRows(data.data);
  }

  useEffect(() => {
    load().catch((e) => setMsg(e.message));
  }, [setMsg]);

  return (
    <AdminShell title="Products" userLabel="(session)">
      <div className="flex justify-between mb-4">
        <CrudMessage msg={msg} />
        <Link className="px-4 py-2 rounded bg-slate-900 text-white" href="/admin/products/new">
          Add Product
        </Link>
      </div>

      <div className="bg-white border rounded overflow-auto">
        <table className="w-full text-sm">
          <thead className="bg-slate-100">
            <tr>
              <th className="text-left p-3">Code</th>
              <th className="text-left p-3">Name</th>
              <th className="text-left p-3">Billing</th>
              <th className="text-left p-3">Price (Rp)</th>
              <th className="text-left p-3">Actions</th>
            </tr>
          </thead>
          <tbody>
            {rows.map((p) => (
              <tr key={p.id} className="border-t">
                <td className="p-3">{p.code}</td>
                <td className="p-3">{p.name}</td>
                <td className="p-3">{p.billing_cycle}</td>
                <td className="p-3">{p.price}</td>
                <td className="p-3 flex gap-2">
                  <Link className="px-3 py-1 rounded bg-slate-200 hover:bg-slate-300" href={`/admin/products/${p.id}/edit`}>
                    Edit
                  </Link>
                  <DangerButton
                    onClick={async () => {
                      await remove(`/products/${p.id}`);
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