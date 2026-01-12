{inv?.payments?.length ? (
  <div className="bg-white border rounded-2xl p-6 shadow-sm space-y-2 text-sm">
    <div className="font-semibold">Payments</div>
    <div className="overflow-auto">
      <table className="min-w-full text-sm">
        <thead className="bg-slate-50">
          <tr>
            <th className="text-left px-3 py-2 border-b">Paid At</th>
            <th className="text-left px-3 py-2 border-b">Amount</th>
            <th className="text-left px-3 py-2 border-b">Method</th>
            <th className="text-left px-3 py-2 border-b">Reference</th>
          </tr>
        </thead>
        <tbody>
          {inv.payments.map((p:any) => (
            <tr key={p.id} className="odd:bg-white even:bg-slate-50">
              <td className="px-3 py-2 border-b">{p.paid_at}</td>
              <td className="px-3 py-2 border-b">{p.amount}</td>
              <td className="px-3 py-2 border-b">{p.payment_method}</td>
              <td className="px-3 py-2 border-b">{p.reference_number ?? ""}</td>
            </tr>
          ))}
        </tbody>
      </table>
    </div>
  </div>
) : null}