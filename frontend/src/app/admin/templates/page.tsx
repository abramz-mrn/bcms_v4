"use client";

import AdminShell from "@/components/AdminShell";
import Link from "next/link";
import { useEffect, useState } from "react";
import { apiGet } from "@/lib/api";
import { CrudMessage, DangerButton, useCrudSubmit } from "@/components/crud";

export default function TemplatesPage() {
  const [rows, setRows] = useState<any[]>([]);
  const { msg, setMsg, remove } = useCrudSubmit();

  async function load() {
    const data = await apiGet("/templates", { credentials: "include" });
    setRows(data.data);
  }

  useEffect(() => {
    load().catch((e) => setMsg(e.message));
  }, [setMsg]);

  return (
    <AdminShell title="Templates" userLabel="(session)">
      <div className="flex justify-between mb-4">
        <CrudMessage msg={msg} />
        <Link className="px-4 py-2 rounded bg-slate-900 text-white" href="/admin/templates/new">
          Add Template
        </Link>
      </div>

      <div className="bg-white border rounded overflow-auto">
        <table className="w-full text-sm">
          <thead className="bg-slate-100">
            <tr>
              <th className="text-left p-3">Type</th>
              <th className="text-left p-3">Name</th>
              <th className="text-left p-3">Active</th>
              <th className="text-left p-3">Actions</th>
            </tr>
          </thead>
          <tbody>
            {rows.map((t) => (
              <tr key={t.id} className="border-t">
                <td className="p-3">{t.type}</td>
                <td className="p-3">{t.name}</td>
                <td className="p-3">{String(t.is_active)}</td>
                <td className="p-3 flex gap-2">
                  <Link className="px-3 py-1 rounded bg-slate-200 hover:bg-slate-300" href={`/admin/templates/${t.id}/edit`}>
                    Edit
                  </Link>
                  <DangerButton
                    onClick={async () => {
                      await remove(`/templates/${t.id}`);
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