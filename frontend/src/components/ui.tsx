export function Card({ title, children }: { title: string; children: React.ReactNode }) {
  return (
    <div className="bg-white border rounded">
      <div className="px-4 py-3 border-b font-medium">{title}</div>
      <div className="p-4">{children}</div>
    </div>
  );
}

export function Field({
  label,
  name,
  type = "text",
  defaultValue,
}: {
  label: string;
  name: string;
  type?: string;
  defaultValue?: string;
}) {
  return (
    <div className="space-y-1">
      <label className="text-sm">{label}</label>
      <input className="w-full border rounded px-3 py-2" name={name} type={type} defaultValue={defaultValue} />
    </div>
  );
}

export function Submit({ label }: { label: string }) {
  return (
    <button className="px-4 py-2 rounded bg-slate-900 text-white hover:bg-slate-800">
      {label}
    </button>
  );
}