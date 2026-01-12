import React from "react";

export default async function PayInvoicePage({ params }: { params: { token: string } }) {
  const base = process.env.NEXT_PUBLIC_API_BASE_URL || "http://127.0.0.1/api";
  const r = await fetch(`${base}/v1/public/invoices/by-token/${params.token}`, { cache: "no-store" });

  if (r.status === 410) {
    return <div className="p-6">Payment link expired.</div>;
  }
  if (!r.ok) {
    return <div className="p-6">Invoice not found.</div>;
  }

  const inv = await r.json();

  return (
    <div className="max-w-xl mx-auto p-6 space-y-4">
      <h1 className="text-2xl font-semibold">Invoice Payment</h1>

      <div className="border rounded-2xl p-4 bg-white">
        <div><strong>Invoice:</strong> {inv.invoice_number ?? inv.id}</div>
        <div><strong>Status:</strong> {inv.status}</div>
        <div><strong>Issue date:</strong> {inv.issue_date}</div>
        <div><strong>Due date:</strong> {inv.due_date}</div>
        <div className="mt-2 text-lg"><strong>Total:</strong> {inv.total}</div>
      </div>

      <div className="border rounded-2xl p-4 bg-white">
        <h2 className="font-semibold mb-2">Cara Pembayaran</h2>
        <ol className="list-decimal pl-5 space-y-1 text-sm">
          <li>Transfer ke rekening yang tertera pada invoice (atau VA jika ada).</li>
          <li>Konfirmasi pembayaran ke admin jika diperlukan.</li>
          <li>Status akan berubah menjadi Paid setelah pembayaran diverifikasi.</li>
        </ol>
      </div>
    </div>
  );
}