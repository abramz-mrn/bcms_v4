<td className="px-3 py-2 border-b">
  <div className="flex flex-wrap gap-2">
    <a
      className="px-3 py-1 rounded-lg border bg-slate-50 hover:bg-slate-100 text-sm"
      href={`/customers/${id}/provisionings/${p.id}/edit`}
    >
      Edit
    </a>
    <PingButton provisioningId={p.id} />
    <DeleteProvisioningButton provisioningId={p.id} onDone={load} />
  </div>
</td>