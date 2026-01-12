import Link from "next/link";

export default function Home() {
  return (
    <main className="min-h-screen flex items-center justify-center p-6">
      <div className="w-full max-w-md bg-white rounded-xl shadow p-6 space-y-4">
        <h1 className="text-2xl font-semibold">BCMS v4</h1>
        <p className="text-sm text-slate-600">Billing & Customer Management System (ISP)</p>
        <div className="flex gap-3">
          <Link className="px-4 py-2 rounded bg-slate-900 text-white" href="/login">Login</Link>
          <a className="px-4 py-2 rounded border" href="/health">Health</a>
        </div>
      </div>
    </main>
  );
}